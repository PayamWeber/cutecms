<?php

namespace App\Helpers;

use App\Models\Language;
use Zend\I18n\Translator\Translator;

class LangHelper
{
    public $lang  = 'en_US';
    public $langs = [
        'en_US',
        'fa_IR',
    ];

    public static $default_textdomain = 'default';
    public static $default_lang       = 'en_US';

    public function __construct() { }

    public function set_languages()
    {
        $user_lang          = isset( auth()->user()->id ) ? auth()->user()->meta( Language::KEY ) : '';
        $user_lang          = in_array( $user_lang, $this->langs ) ? $user_lang : $this->lang;
        $GLOBALS[ 'lang' ]  = $user_lang;
        $GLOBALS[ 'langs' ] = $this->langs;
    }

    public function make_translator()
    {
        $GLOBALS[ 'translator' ] = new Translator();
        $GLOBALS[ 'translator' ]->setLocale( $GLOBALS[ 'lang' ] );
        foreach ( $this->langs as $lang )
        {
            if ( file_exists( resource_path( "lang/$lang/$lang.mo" ) ) )
                $GLOBALS[ 'translator' ]->addTranslationFile( 'gettext', resource_path( "lang/$lang/$lang.mo" ), self::$default_textdomain, $lang );
        }
        global $langs;
        foreach ( $langs as $lang )
        {
            $GLOBALS[ 'translations' ][self::$default_textdomain][$lang] = (array) $GLOBALS[ 'translator' ]->getAllMessages( self::$default_textdomain, $lang );
        }
    }

    public static function translate( $text, $textdomain = '', $locale = '' )
    {
        global $lang;
        $textdomain = $textdomain ?? self::$default_textdomain;
        $locale = $locale ? $locale : $lang;

        global $translator;

        if ( ! $translator )
            return $text;

        $translations = $GLOBALS['translations'][$textdomain][$locale] ?? [];

//        dd( $GLOBALS['translations'] );

        if ( isset( $translations[ $text ] ) )
            return $translations[ $text ] ?? $text;
        else
        {
            self::update_langauge( $text, '', $textdomain, $locale );
            return $text;
        }
    }

    public static function update_langauge( $string, $translation = '', $textdomain = '', $locale = '' )
    {
        global $lang;
        $locale = $locale ? $locale : $lang;
        $textdomain = $textdomain ? $textdomain : self::$default_textdomain;
        global $translator;

        if ( ! $translator )
            return false;

        if ( ! $string )
            return false;

        $translations = $GLOBALS['translations'][$textdomain][$locale] ?? [];
        $translations     = array_merge( $translations, [
            $string => $translation ? $translation : $string,
        ] );
        $new_translations = [];
        foreach ( $translations as $s => $t )
        {
            $s = strip_tags( $s );
            $t = strip_tags( $t );
            $new_translations[ $s ] = [
                'msgid' => $s,
                'msgstr' => [ $t ],
            ];
        }

        $GLOBALS['translations'][$textdomain][$locale] = $translations;
        self::update_mo_file( $new_translations, resource_path( "lang/$locale/$locale.mo" ), $locale );
    }

    private static function update_mo_file( $translations, $output_file, $locale )
    {
        // sort by msgid
//        ksort( $translations, SORT_STRING );
        // our mo file data
        $mo = '';
        $po = self::initial_po_text( $locale );
        // header data
        $offsets = [];
        $ids     = '';
        $strings = '';
        foreach ( $translations as $entry )
        {
            $id = $entry[ 'msgid' ];
            if ( isset ( $entry[ 'msgid_plural' ] ) )
                $id .= "\x00" . $entry[ 'msgid_plural' ];
            // context is merged into id, separated by EOT (\x04)
            if ( array_key_exists( 'msgctxt', $entry ) )
                $id = $entry[ 'msgctxt' ] . "\x04" . $id;
            // plural msgstrs are NUL-separated
            $str = implode( "\x00", $entry[ 'msgstr' ] );
            // keep track of offsets
            $offsets[] = [
                strlen( $ids
                ), strlen( $id ), strlen( $strings ), strlen( $str ),
            ];
            // plural msgids are not stored (?)
            $ids     .= $id . "\x00";
            $strings .= $str . "\x00";

            // add to po file
            $po .= "msgid \"$entry[msgid]\"\n";
            $po .= "msgstr \"$str\"\n";
            $po .= "\n";
        }
        // keys start after the header (7 words) + index tables ($#hash * 4 words)
        $key_start = 7 * 4 + sizeof( $translations ) * 4 * 4;
        // values start right after the keys
        $value_start = $key_start + strlen( $ids );
        // first all key offsets, then all value offsets
        $key_offsets   = [];
        $value_offsets = [];
        // calculate
        foreach ( $offsets as $v )
        {
            list ( $o1, $l1, $o2, $l2 ) = $v;
            $key_offsets[]   = $l1;
            $key_offsets[]   = $o1 + $key_start;
            $value_offsets[] = $l2;
            $value_offsets[] = $o2 + $value_start;
        }
        $offsets = array_merge( $key_offsets, $value_offsets );
        // write header
        $mo .= pack( 'Iiiiiii', 0x950412de, // magic number
            0, // version
            sizeof( $translations ), // number of entries in the catalog
            7 * 4, // key index offset
            7 * 4 + sizeof( $translations ) * 8, // value index offset,
            0, // hashtable size (unused, thus 0)
            $key_start // hashtable offset
        );
        // offsets
        foreach ( $offsets as $offset )
            $mo .= pack( 'i', $offset );
        // ids
        $mo .= $ids;
        // strings
        $mo .= $strings;

        if ( ! is_dir( str_replace( basename( $output_file ), '', $output_file ) ) )
            mkdir( str_replace( basename( $output_file ), '', $output_file ) );
        $file = fopen( $output_file, 'w' );
        fwrite( $file, $mo );
        fclose( $file );

        // make .po file
        $file = fopen( str_replace( '.mo', '.po', $output_file ), 'w' );
        fwrite( $file, $po );
        fclose( $file );
    }

    public static function initial_po_text( $locale )
    {
        return "
        msgid \"\"\n
        msgstr \"\"\n
        \"Project-Id-Version: WeberZ\\n\"\n
        \"POT-Creation-Date: 2019-01-22 16:42+0330\\n\"\n
        \"PO-Revision-Date: 2019-01-22 16:55+0330\\n\"\n
        \"Last-Translator: \\n\"\n
        \"Language-Team: \\n\"\n
        \"Language: $locale\\n\"\n
        \"MIME-Version: 1.0\\n\"\n
        \"Content-Type: text/plain; charset=UTF-8\\n\"\n
        \"Content-Transfer-Encoding: 8bit\\n\"\n
        \"X-Generator: Poedit 2.1.1\\n\"\n
        \"X-Poedit-Basepath: ..\\n\"\n
        \"Plural-Forms: nplurals=2; plural=(n==0 || n==1);\\n\"\n
        \"X-Poedit-Flags-xgettext: --add-comments=translators:\\n\"\n
        \"X-Poedit-WPHeader: style.css\\n\"\n
        \"X-Poedit-SourceCharset: UTF-8\\n\"\n
        \"X-Poedit-KeywordsList: __;_e;_n:1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;esc_attr__;\"\n
        \"esc_attr_e;esc_attr_x:1,2c;esc_html__;esc_html_e;esc_html_x:1,2c;\"\n
        \"_n_noop:1,2;_nx_noop:3c,1,2;__ngettext_noop:1,2\\n\"\n
        \"X-Poedit-SearchPath-0: .\\n\"\n
        \"X-Poedit-SearchPathExcluded-0: *.js\\n\"\n
        \n
        ";
    }
}
