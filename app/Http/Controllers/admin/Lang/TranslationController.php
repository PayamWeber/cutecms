<?php

namespace App\Http\Controllers\Admin\Lang;

use App\Helpers\AlertHelper;
use App\Helpers\LangHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    protected $form;
    protected $validation;
    public    $data;

    public function __construct( Request $request )
    {
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function string_translation( Request $request )
    {
        $this->data['models'] = $this->filter( $request );
        return view( 'admin.lang.string_translation.index', $this->data );
    }

    public function update_string( Request $request )
    {
        if ( ! $request->get('string') || ! $request->get('text_domain') || ! $request->get('lang') )
            die();

        LangHelper::update_langauge(
            $request->get('string'),
            $request->get('translation', ''),
            $request->get('text_domain'),
            $request->get('lang') );
        die( json_encode( [
            'type' => 'success'
        ] ) );
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    protected function filter( Request $request )
    {
        global $lang;
        global $translations;
        $models = [];
        $this->data[ 'rows_per_page' ]       = 25;
        $this->data[ 'current_page_number' ] = $request->get( 'page_number', 1 );
        $count = 0;
        foreach ( $translations as $text_domain => $translation )
        {
            $models[$text_domain] = array_reverse( $translation[$request->get( 'lang', $lang )] );
            $count += count( $models[$text_domain] );
        }
        $this->data[ 'counter' ]             = $count - ( $this->data[ 'rows_per_page' ] * ( $this->data[ 'current_page_number' ] - 1 ) );

        if ( $request->get('search') )
        {
            foreach ( $models as  $text_domain => $model )
            {
                foreach ( $model as $string => $translation )
                {
                    if ( strpos( strtolower( $string ), strtolower( $request->get('search') ) ) === false && strpos( strtolower( $translation ), strtolower( $request->get('search') ) ) === false )
                        unset( $models[$text_domain][$string] );
                }
            }
        }

        return $models;
    }
}
