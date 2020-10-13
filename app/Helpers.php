<?php

use App\Helpers\AlertHelper;
use App\Helpers\LangHelper;
use App\Models\Capability;
use App\Models\Option;
use App\User;
use App\Models\Media;

/**
 * this function return an option value by giving a name of that
 *
 * @param        $name
 * @param string $default
 * @param bool   $is_list_item
 *
 * @return mixed
 */
function get_option( $name, $default = '', $is_list_item = false )
{
	return $option = Option::get_option( $name, $default, $is_list_item );
}

/**
 * this function make an option in options table
 *
 * @param $name
 * @param $value
 * @param $is_autoload
 *
 * @return bool
 */
function add_option( $name, $value = '', $is_autoload = false )
{
	return Option::add_option( $name, $value, $is_autoload );
}

/**
 * this function update an option in options table
 *
 * @param $name
 * @param $value
 * @param $is_autoload
 *
 * @return bool
 */
function update_option( $name, $value = '', $is_autoload = false )
{
	return Option::update_option( $name, $value, $is_autoload );
}

/**
 * urls
 *
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 */

/**
 * @param string $path
 *
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 */
function url_admin( $path = '' )
{
	if ( $path && mb_substr( $path, 0, 1 ) != '/' )
		$path = '/' . $path;
	return url( '/admin' . $path );
}

/**
 * @param string $path
 *
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 */
function admin_url( $path = '' )
{
	return url_admin( $path );
}

/**
 * @param string $path
 *
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 */
function admin_assets_url( $path = '' )
{
	if ( $path && mb_substr( $path, 0, 1 ) != '/' )
		$path = '/' . $path;
	return url( '/public/assets/admin' . $path );
}

/**
 * @return mixed
 */
function jDate( $time = '' )
{
	$time = $time ? $time : time();
	return new \App\Helpers\jDate( $time );
}

/**
 * @return mixed
 */
function jDateTime()
{
	return new \App\Helpers\jDateTime;
}

/**
 * is admin or not
 *
 * @return bool
 */
function is_admin( $id = null )
{
	return optional( $id ? User::find( $id ) : \Illuminate\Support\Facades\Auth::user() )->isAdmin();
}

/**
 * this function check user capability
 *
 * @param        $capability
 * @param string $operator this argument can receive 'AND' or 'OR'
 * @param null   $user_id
 *
 * @return bool
 */
function is_user_can( $capability, $operator = 'OR', $user_id = null )
{
	return User::isUserCan( $capability, $operator, $user_id );
}

/**
 * @param $capability_name
 *
 * @return array|mixed|object
 */
function get_cap_route( $capability_name )
{
	return Capability::get_route_by_name( $capability_name );
}

/**
 * set document type
 *
 * @param string $type type of document
 */
function set_content_type( $type = 'application/json' )
{
	header( 'Content-Type: ' . $type );
}

/**
 * Read CSV from URL or File
 *
 * @param  string $filename  Filename
 * @param  string $delimiter Delimiter
 *
 * @return array            [description]
 */
function read_csv( $filename, $delimiter = "," )
{
	$file_data = [];
	$handle = @fopen( $filename, "r" ) or false;
	if ( $handle !== FALSE )
	{
		while ( ( $data = fgetcsv( $handle, 1000, $delimiter ) ) !== FALSE )
		{
			$file_data[] = $data;
		}
		fclose( $handle );
	}
	return $file_data;
}

/**
 * Print Log to the page
 *
 * @param  mixed   $var    Mixed Input
 * @param  boolean $pre    Append <pre> tag
 * @param  boolean $return Return Output
 *
 * @return string/void     Dependent on the $return input
 */
function plog( $var, $pre = true, $return = false )
{
	$info   = print_r( $var, true );
	$result = $pre ? "<pre>$info</pre>" : $info;
	if ( $return ) return $result;
	else echo $result;
}

/**
 * Log to file
 *
 * @param  string $log Log
 *
 * @return void
 */
function elog( $log, $fn = "debug.log" )
{
	$fp = fopen( $fn, "a" );
	fputs( $fp, "[" . date( "d-m-Y h:i:s" ) . "][Log] $log\r\n" );
	fclose( $fp );
}

/**
 * @return SmartUI
 */
function SmartUI()
{
	return new SmartUI;
}

/**
 * this function parse arguments
 *
 * @param array $array1
 * @param array $array2
 *
 * @return array
 */
function pmw_parse_args( array $array1, array $array2 )
{
	foreach ( $array1 as $key => $value )
	{
		$array1[ $key ] = $array1[ $key ] ?? '';

		if ( isset( $array2[ $key ] ) )
		{
			if ( $array2[ $key ] && is_array( $array2[ $key ] ) && is_array( $array1[ $key ] ) )
			{
				$array1[ $key ] = pmw_parse_args( $array1[ $key ], $array2[ $key ] );
			} else
			{
				$array1[ $key ] = $array2[ $key ];
				unset( $array2[ $key ] );
			}
		}
	}

	return $array1 + $array2;
}

/**
 * this function make a smartUi form with an array of fields
 *
 * @param array $args
 * @param array $fields
 * @param array $fieldsets
 *
 * @return mixed
 */
function make_smart_ui_form( array $args = [], array $fields, array $fieldsets = [] )
{
	$default_args = [
		'form_options' => [],
		'title' => 'افزودن یک دسترسی',
		'submit_text' => 'انتشار',
		'footer_buttons' => '',
	];
	$args         = pmw_parse_args( $default_args, $args );

	$default_options = [
		'method' => 'post',
		'action' => '',
	];
	$options         = pmw_parse_args( $default_options, $args[ 'form_options' ] );

	$ui = new SmartUI;
	$ui->start_track();

	$form = $ui->create_smartform( $fields );
	$form->title( $args[ 'title' ] );

	/**
	 * set options
	 */
	if ( $options )
	{
		foreach ( $options as $option_key => $option_value )
		{
			$form->options( $option_key, $option_value );
		}
	}

	/**
	 * set fieldsets
	 */
	if ( $fieldsets )
	{
		foreach ( $fieldsets as $key => $fieldset )
		{
			$form->fieldset( $key, $fieldset );
		}
	}

	$form->footer( function () use ( $ui, $args ) {
		$html = '';
		if ( $args[ 'footer_buttons' ] && is_array( $args[ 'footer_buttons' ] ) )
		{
			foreach ( $args[ 'footer_buttons' ] as $button )
			{
				$button[ 'attr' ]      = $button[ 'attr' ] ?? '';
				$button[ 'attr_text' ] = '';
				$button[ 'title' ]     = $button[ 'title' ] ?? '';
				$button[ 'color' ]     = $button[ 'color' ] ?? 'primary';

				// generate attributes
				if ( $button[ 'attr' ] )
				{
					foreach ( $button[ 'attr' ] as $attr_key => $attr_value )
					{
						$button[ 'attr_text' ] .= " $attr_key='$attr_value' ";
					}
				}

				$button = (object) $button;
				$html   .= "<a class=\"btn btn-$button->color btn-glow \" $button->attr_text>$button->title</a>";
			}
		}
		$html .= $ui->create_button( $args[ 'submit_text' ], 'primary btn-glow ml-1' )->attr( [ 'type' => 'submit' ] )->print_html( true );
		return $html;
	} );

	$result = $form->print_html( true );

	return $result;
}

/**
 * @param array $args
 *
 * @return mixed
 */
function make_data_table( array $args = [] )
{
	$default_args = [
		'title' => '',
		'color' => 'primary',
		'rowSet' => [ 'id' => 'ردیف' ],
		'rows' => '',
		'customRows' => '',
		'pagination' => '',
		'top_button' => [
			'title' => '',
			'url' => '',
			'icon' => 'glyphicon-plus',
		],
	];
	$args         = pmw_parse_args( $default_args, $args );

	$ui      = new SmartUI;
	$options = [ "editbutton" => false ];
	$widget  = $ui->create_widget( $options );

	$table_tds = '';
	$table_ths = '';

	if ( $args[ 'rowSet' ] )
	{
		foreach ( $args[ 'rowSet' ] as $row_key => $row_value )
		{
			$table_ths .= "<th>$row_value</th>";
		}
	}

	if ( $args[ 'rows' ] )
	{
		$counter = 1;
		foreach ( $args[ 'rows' ] as $row_key => $row_value )
		{
			$td[ 'name' ]  = $row_value->name;
			$td[ 'title' ] = $row_value->title;
			$td[ 'id' ]    = $row_value->id;
			$table_tds     .= "<tr>";
			if ( $args[ 'rowSet' ] )
			{
				foreach ( $args[ 'rowSet' ] as $th_key => $th_value )
				{
					if ( isset( $args[ 'customRows' ][ $th_key ] ) )
					{
						$callable  = $args[ 'customRows' ][ $th_key ];
						$table_tds .= "<td>" . $callable( $row_key, $row_value, $counter ) . "</td>";
					} else if ( isset( $td[ $th_key ] ) )
					{
						$table_tds .= "<td>" . $td[ $th_key ] . "</td>";
					}
				}
			}
			$table_tds .= "</tr>";
			$counter++;
		}
	}

	$pagination      = $args[ 'pagination' ][ 'links' ] ?? '';
	$paged           = $_GET[ 'page' ] ?? 1;
	$last_page       = $args[ 'rows' ]->lastPage();
	$pagination_text = ( $last_page > 1 ) ? "صفحه <span class=\"txt-color-darken\">$paged</span> از <span class=\"text-primary\">$last_page</span>" : '';
	$search_title    = lang( 'Search' );

	$data_table = <<<html
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div id="invoices-list_filter" class="dataTables_filter">
                    <form action="" method="get">
                        <label>
                            <input name="search" type="search" class="form-control form-control-sm" placeholder="$search_title" aria-controls="invoices-list">
                        </label>
                    </form>
                </div>
            </div>
        </div>
        <table id="dt_basic" class="table table-striped table-bordered sourced-data dataTable" width="100%">
            <thead>
                <tr>
                    $table_ths
                </tr>
            </thead>
            <tbody>
                $table_tds
            </tbody>
        </table>
html;
	if ( $pagination_text )
		$data_table .= <<<html
<div class="dt-toolbar-footer col-xs-12">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="dataTables_paginate paging_simple_numbers" id="dt_basic_paginate">
                $pagination
            </div>
        </div>
        <div class="col-sm-6 col-xs-12 hidden-xs">
            <div class="dataTables_info text-right" id="dt_basic_info" role="status" aria-live="polite">
                $pagination_text
            </div>
        </div>
    </div>
</div>
html;

// using standard
	$widget->body   = [
		"content" => $data_table,
	];
	$widget->header = [
		"title" => "<h2>" . $args[ 'title' ] . "</h2>",
		"icon" => 'fa fa-check',
		'button' => [
			'title' => $args[ 'top_button' ][ 'title' ],
			'url' => $args[ 'top_button' ][ 'url' ],
			'icon' => $args[ 'top_button' ][ 'icon' ],
		],
	];
	$widget->color  = $args[ 'color' ];

	return $widget->print_html( true );
}

/**
 * @param array $args
 *
 * @return string
 */
function widget_before( array $args = [] )
{
	$default_args = [
		'id' => '',
		'title' => lang( 'Widget' ),
		'color' => 'white',
		'button' => [
			'title' => '',
			'url' => '',
			'icon' => 'glyphicon-plus',
		],
	];
	$args         = pmw_parse_args( $default_args, $args );

	$args[ 'button_html' ] = '';
	if ( $args[ 'button' ][ 'title' ] )
	{
		$args[ 'button_html' ] = '<a href="' . $args[ 'button' ][ 'url' ] . '" class="btn btn-sm bg-white ' . $args[ 'color' ] . '"><i class="' . $args[ 'button' ][ 'icon' ] . ' ' . $args[ 'color' ] . '"></i> ' . $args[ 'button' ][ 'title' ] . '</a>';
	}
	$white_header_class = '';
	$white_text_class   = '';
	if ( $args[ 'color' ] != 'white' )
	{
		$white_header_class = 'card-head-inverse';
		$white_text_class   = 'text-white';
	}

	return <<<HTML
    <div class="card" id="$args[id]">
        <div class="card-header $white_header_class bg-$args[color]">
          <h4 class="card-title $white_text_class">$args[title]</h4>
          <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
          <div class="heading-elements">
            <ul class="list-inline mb-0">
              <li>$args[button_html]</li>
              <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
              <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              <li><a data-action="close"><i class="ft-x"></i></a></li>
            </ul>
          </div>
        </div>
        <div class="card-content collapse show">
          <div class="card-body">
HTML;
}

/**
 * @return string
 */
function widget_after()
{
	return "</div></div></div>";
}

/**
 * @param        $text
 * @param string $textdomain
 *
 * @return string
 */
function lang( $text, $textdomain = 'default', $locale = '' )
{
	return LangHelper::translate( $text, $textdomain, $locale );
}

/**
 * @param $url
 *
 * @return string
 */
function clean_url( $url )
{
	$last_character = mb_substr( $url, strlen( $url ) - 1, 1 );

	if ( $last_character == '/' )
		$url = mb_substr( $url, 0, strlen( $url ) - 1 );

	$url = str_replace( '//', '/', $url );
	$url = str_replace( ':/', '://', $url );

	return $url;
}

/**
 * @param $bytes
 *
 * @return string
 */
function media_get_file_size( $bytes )
{
	if ( $bytes >= 1073741824 )
	{
		$bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
	} else if ( $bytes >= 1048576 )
	{
		$bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
	} else if ( $bytes >= 1024 )
	{
		$bytes = number_format( $bytes / 1024, 2 ) . ' KB';
	} else if ( $bytes > 1 )
	{
		$bytes = $bytes . ' bytes';
	} else if ( $bytes == 1 )
	{
		$bytes = $bytes . ' byte';
	} else
	{
		$bytes = '0 bytes';
	}

	return $bytes;
}

/**
 * @param array $args
 *
 * @return string
 */
function make_media_uploader( array $args = [] )
{
	return Media::make_media_uploader( $args );
}

/**
 * @param        $media_id
 * @param string $size
 *
 * @return bool|\Illuminate\Contracts\Routing\UrlGenerator|string
 */
function get_media_url( $media_id, $size = 'full' )
{
	return optional($media_id instanceof Media ? $media_id : Media::find($media_id))->url( $size );
}

/**
 * @param        $message
 * @param string $type
 *
 * @return null|string|string[]
 */
function print_alert( $message, $type = 'info' )
{
	return AlertHelper::print( $message, $type );
}

/**
 * @param $fine_name
 *
 * @return mixed
 */
function clean_string( $fine_name )
{
	return str_replace( [
		'!', '@', '#', '$', '.',
		'%', '^', '&', '*', '?',
		'~', '`', '+', '=', ':',
		'/', '\\', '|', '<', '>',
	], '', $fine_name );
}

/**
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 */
function get_template_directory_uri()
{
	global $theme;
	return url( 'public/themes/' . $theme[ 'name' ] );
}

/**
 * @return string
 */
function get_template_directory_path()
{
	global $theme;
	return public_path( 'public/themes/' . $theme[ 'name' ] );
}


/**
 * @return string
 */
function get_current_url()
{
    $http = isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] ? 'https://' : 'http://';
    return $http . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
}

/**
 * @param       $success
 * @param string|array $errors_or_data
 * @param int   $code
 *
 * @return \Illuminate\Http\JsonResponse
 */
function api_response( $success, $errors_or_data = [], $code = 0 )
{
    return response()->json(
        [
            'status' => boolval( $success ),
            $success ? 'data' : 'errors' => $errors_or_data,
        ],
        $code ? $code : ( $success ? \Illuminate\Http\Response::HTTP_OK : \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY ),
        [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8',
        ],
        JSON_UNESCAPED_UNICODE
    );
}

/**
 * this function has default status code 200 for mobile responses
 *
 * @param       $success
 * @param array $errors_or_data
 * @param int   $code
 *
 * @return \Illuminate\Http\JsonResponse
 */
function _api_response( $success, $errors_or_data = [], $code = 0, $real_status = false )
{
    $code = $code ? $code : ( $success ? \Illuminate\Http\Response::HTTP_OK : \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY );

    return response()->json(
        [
            'status' => boolval( $success ),
            $success ? 'data' : 'error' => $errors_or_data,
        ] + ( ! $success ? [
            'error_code' => $code,
        ] : [] ),
        $real_status ? $code : \Illuminate\Http\Response::HTTP_OK,
        [
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8',
        ],
        JSON_UNESCAPED_UNICODE
    );
}

/**
 * Checks and cleans a URL.
 *
 * A number of characters are removed from the URL. If the URL is for displaying
 * (the default behaviour) ampersands are also replaced. The {@see 'clean_url'} filter
 * is applied to the returned cleaned URL.
 *
 * @param string $url       The URL to be cleaned.
 * @param array  $protocols Optional. An array of acceptable protocols.
 *                          Defaults to return value of wp_allowed_protocols()
 * @param string $_context  Private. Use esc_url_raw() for database usage.
 *
 * @return string The cleaned $url after the {@see 'clean_url'} filter is applied.
 */
function esc_url( $url, $protocols = null, $_context = 'display' )
{
    if ( '' == $url )
    {
        return $url;
    }

    $url = str_replace( ' ', '%20', $url );
    $url = preg_replace( '|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url );

    if ( '' === $url )
    {
        return $url;
    }

    if ( 0 !== stripos( $url, 'mailto:' ) )
    {
        $strip = [ '%0d', '%0a', '%0D', '%0A' ];
        $url   = _deep_replace( $strip, $url );
    }

    $url = str_replace( ';//', '://', $url );
    /* If the URL doesn't appear to contain a scheme, we
     * presume it needs http:// prepended (unless a relative
     * link starting with /, # or ? or a php file).
     */
    if ( strpos( $url, ':' ) === false && ! in_array( $url[ 0 ], [ '/', '#', '?' ] ) &&
        ! preg_match( '/^[a-z0-9-]+?\.php/i', $url ) )
    {
        $url = 'http://' . $url;
    }

    // Replace ampersands and single quotes only when displaying.
    if ( 'display' == $_context )
    {
        $url = str_replace( '&amp;', '&#038;', $url );
        $url = str_replace( "'", '&#039;', $url );
    }

    return $url;
}

/**
 * Checks for invalid UTF8 in a string.
 *
 * @staticvar bool $is_utf8
 * @staticvar bool $utf8_pcre
 *
 * @param string $string The text which is to be checked.
 * @param bool   $strip  Optional. Whether to attempt to strip out invalid UTF8. Default is false.
 *
 * @return string The checked text.
 */
function check_invalid_utf8( $string, $strip = false )
{
    $string = (string) $string;

    if ( 0 === strlen( $string ) )
    {
        return '';
    }

    // Store the site charset as a static to avoid multiple calls to get_option()
    static $is_utf8 = null;
    if ( ! isset( $is_utf8 ) )
    {
        $is_utf8 = in_array( get_option( 'blog_charset' ), [ 'utf8', 'utf-8', 'UTF8', 'UTF-8' ] );
    }
    if ( ! $is_utf8 )
    {
        return $string;
    }

    // Check for support for utf8 in the installed PCRE library once and store the result in a static
    static $utf8_pcre = null;
    if ( ! isset( $utf8_pcre ) )
    {
        $utf8_pcre = @preg_match( '/^./u', 'a' );
    }
    // We can't demand utf8 in the PCRE installation, so just return the string in those cases
    if ( ! $utf8_pcre )
    {
        return $string;
    }

    // preg_match fails when it encounters invalid UTF8 in $string
    if ( 1 === @preg_match( '/^./us', $string ) )
    {
        return $string;
    }

    // Attempt to strip the bad chars if requested (not recommended)
    if ( $strip && function_exists( 'iconv' ) )
    {
        return iconv( 'utf-8', 'utf-8', $string );
    }

    return '';
}

/**
 * Perform a deep string replace operation to ensure the values in $search are no longer present
 *
 * Repeats the replacement operation until it no longer replaces anything so as to remove "nested" values
 * e.g. $subject = '%0%0%0DDD', $search ='%0D', $result ='' rather than the '%0%0DD' that
 * str_replace would return
 *
 * @param string|array $search  The value being searched for, otherwise known as the needle.
 *                              An array may be used to designate multiple needles.
 * @param string       $subject The string being searched and replaced on, otherwise known as the haystack.
 *
 * @return string The string with the replaced values.
 */
function _deep_replace( $search, $subject )
{
    $subject = (string) $subject;

    $count = 1;
    while ( $count )
    {
        $subject = str_replace( $search, '', $subject, $count );
    }

    return $subject;
}

/**
 * Converts a number of special characters into their HTML entities.
 *
 * Specifically deals with: &, <, >, ", and '.
 *
 * $quote_style can be set to ENT_COMPAT to encode " to
 * &quot;, or ENT_QUOTES to do both. Default is ENT_NOQUOTES where no quotes are encoded.
 *
 * @staticvar string $_charset
 *
 * @param string     $string         The text which is to be encoded.
 * @param int|string $quote_style    Optional. Converts double quotes if set to ENT_COMPAT,
 *                                   both single and double if set to ENT_QUOTES or none if set to ENT_NOQUOTES.
 *                                   Also compatible with old values; converting single quotes if set to 'single',
 *                                   double if set to 'double' or both if otherwise set.
 *                                   Default is ENT_NOQUOTES.
 * @param string     $charset        Optional. The character encoding of the string. Default is false.
 * @param bool       $double_encode  Optional. Whether to encode existing html entities. Default is false.
 *
 * @return string The encoded text with HTML entities.
 */
function _specialchars( $string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false )
{
    $string = (string) $string;

    if ( 0 === strlen( $string ) )
    {
        return '';
    }

    // Don't bother if there are no specialchars - saves some processing
    if ( ! preg_match( '/[&<>"\']/', $string ) )
    {
        return $string;
    }

    // Account for the previous behaviour of the function when the $quote_style is not an accepted value
    if ( empty( $quote_style ) )
    {
        $quote_style = ENT_NOQUOTES;
    } else if ( ! in_array( $quote_style, [ 0, 2, 3, 'single', 'double' ], true ) )
    {
        $quote_style = ENT_QUOTES;
    }

    // Store the site charset as a static to avoid multiple calls to wp_load_alloptions()
    if ( ! $charset )
    {
        $charset = 'UTF-8';
    }

    $_quote_style = $quote_style;

    if ( $quote_style === 'double' )
    {
        $quote_style  = ENT_COMPAT;
        $_quote_style = ENT_COMPAT;
    } else if ( $quote_style === 'single' )
    {
        $quote_style = ENT_NOQUOTES;
    }

    $string = htmlspecialchars( $string, $quote_style, $charset, $double_encode );

    // Back-compat.
    if ( 'single' === $_quote_style )
    {
        $string = str_replace( "'", '&#039;', $string );
    }

    return $string;
}

/**
 * Escaping for HTML blocks.
 *
 * @since 2.8.0
 *
 * @param string $text
 *
 * @return string
 */
function esc_html( $text )
{
    $safe_text = check_invalid_utf8( $text );
    $safe_text = _specialchars( $safe_text, ENT_QUOTES );

    return $safe_text;
}

/**
 * get micro time
 *
 * @return array|string|string[]|null
 */
function get_micro_time()
{
    $microtime = explode( ' ', microtime() );
    $microtime = $microtime[ 0 ] ?? '';
    $microtime = $microtime ? preg_replace( '/.*\.(.*)/', '$1', $microtime ) : '';

    return $microtime;
}

/**
 * strip single tag ( do the strip_tags job but only for one tag )
 *
 * @param $str
 * @param $tag
 *
 * @return string|string[]|null
 */
function strip_single_tag( $str, $tag )
{
    $str1 = preg_replace( '/<\/' . $tag . '>/i', '', $str );

    if ( $str1 != $str )
    {
        $str = preg_replace( '/<' . $tag . '[^>]*>/i', '', $str1 );
    }

    return $str;
}

/**
 * create ( if not exists ) and write a file
 */
function write_file( $file, $content )
{
    $file = fopen( $file, 'w' );
    fwrite( $file, $content );
    fclose( $file );
}

/**
 * return contents of a file
 */
function read_file( $file_path )
{
    if ( ! file_exists( $file_path ) )
        return '';

    $file    = fopen( $file_path, 'r' );
    $content = fread( $file, filesize( $file_path ) ? : 1 );
    fclose( $file );

    return $content;
}

/**
 * @param $src
 */
function rmdirr( $src )
{
    if ( file_exists( $src ) )
    {
        $dir = opendir( $src );
        while ( false !== ( $file = readdir( $dir ) ) )
        {
            if ( ( $file != '.' ) && ( $file != '..' ) )
            {
                $full = $src . '/' . $file;
                if ( is_dir( $full ) )
                {
                    rmdirr( $full );
                } else
                {
                    unlink( $full );
                }
            }
        }
        closedir( $dir );
        rmdir( $src );
    }
}

/**
 * @param $src
 * @param $dst
 */
function copy_directory( $src, $dst )
{
    if ( file_exists( $src ) )
    {
        $dir = opendir( $src );
        if ( ! file_exists( $dst ) )
            mkdir( $dst, 0775, true );
        while ( false !== ( $file = readdir( $dir ) ) )
        {
            if ( ( $file != '.' ) && ( $file != '..' ) )
            {
                if ( is_dir( $src . '/' . $file ) )
                {
                    copy_directory( $src . '/' . $file, $dst . '/' . $file );
                } else
                {
                    copy( $src . '/' . $file, $dst . '/' . $file );
                }
            }
        }
        closedir( $dir );
    }
}
