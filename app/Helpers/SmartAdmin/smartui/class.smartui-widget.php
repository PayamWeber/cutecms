<?php

class Widget extends SmartUI
{

    private $_options_map = [
        "editbutton" => true,
        "colorbutton" => true,
        "editbutton" => true,
        "togglebutton" => true,
        "deletebutton" => true,
        "fullscreenbutton" => true,
        "custombutton" => true,
        "collapsed" => false,
        "sortable" => true,
        "refreshbutton" => false,
    ];

    private $_structure = [
        "class" => "",
        "color" => "",
        "id" => "",
        "attr" => [],
        "options" => [],
        "header" => [],
        "body" => [],
    ];

    public function __construct( $options = [], $contents = [] )
    {
        $this->_init_structure( $options, $contents );
    }

    private function _init_structure( $user_options, $user_contents )
    {
        $this->_structure = parent::array_to_object( $this->_structure );

        $user_contents_map = [ "header" => [], "body" => "", "color" => "" ];
        $new_user_contents = parent::set_array_prop_def( $user_contents_map, $user_contents );

        $this->_structure->options = parent::set_array_prop_def( $this->_options_map, $user_options );

        $body_structure         = [
            "editbox" => "",
            "content" => "",
            "class" => "",
            "toolbar" => null,
            "footer" => null,
        ];
        $this->_structure->body = parent::set_array_prop_def( $body_structure, $new_user_contents[ "body" ], "content" );

        $header_structure         = [
            "icon" => null,
            "class" => "",
            "title" => "",
            "toolbar" => [],
        ];
        $this->_structure->header = parent::set_array_prop_def( $header_structure, $new_user_contents[ "header" ], "title" );

        $this->_structure->color = $new_user_contents[ "color" ];
        $this->_structure->id    = $new_user_contents[ "id" ] ?? parent::create_id( true );
    }

    public function __set( $name, $value )
    {
        if ( isset( $this->_structure->{$name} ) )
        {
            $this->_structure->{$name} = $value;
            return;
        }
        SmartUI::err( 'Undefined structure property: ' . $name );
    }

    public function __call( $name, $args )
    {
        return parent::_call( $this, $this->_structure, $name, $args );
    }

    public function __get( $name )
    {
        if ( isset( $this->_structure->{$name} ) )
        {
            return $this->_structure->{$name};
        }
        SmartUI::err( 'Undefined structure property: ' . $name );
        return null;
    }

    public function print_html( $return = false )
    {
        $get_property_value = parent::_get_property_value_func();

        $that      = $this;
        $structure = $this->_structure;

        $header['title'] = isset( $structure->header['title'] ) ? strip_tags( $structure->header['title'] ) : '';
        $header['color'] = ( isset( $structure->color ) && $structure->color ) ? $structure->color : 'primary';
        $header['button'] = [
            'title' => $structure->header['button']['title'] ?? '',
            'url' => $structure->header['button']['url'] ?? '',
            'icon' => $structure->header['button']['icon'] ?? '',
        ];
        $header['class'] = $structure->header['class'] ?? '';
        $header['button_html'] = '';
        $content = isset( $structure->body['content'] ) ? $structure->body['content'] : '';

        if ( $header['button']['title'] )
        {
            $header['button_html'] = '<a href="' . $header['button']['url'] . '" class="btn btn-sm bg-white ' . $header['color'] . '"><i class="' . $header['button']['icon'] . ' ' . $header['color'] . '"></i> ' . $header['button']['title'] . '</a>';
        }

        $header = (object)$header;
        $result = <<<HTML
	    <div class="card" id="$structure->id">
            <div class="card-header card-head-inverse bg-$header->color">
              <h4 class="card-title text-white">$header->title</h4>
              <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
              <div class="heading-elements">
                <ul class="list-inline mb-0">
                  <li>$header->button_html</li>
                  <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                  <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                  <li><a data-action="close"><i class="ft-x"></i></a></li>
                </ul>
              </div>
            </div>
            <div class="card-content collapse show">
              <div class="card-body">
                $content
              </div>
            </div>
          </div>
HTML;

        if ( $return ) return $result;
        else echo $result;
    }

    public function print_html_old( $return = false )
    {
        $get_property_value = parent::_get_property_value_func();

        $that      = $this;
        $structure = $this->_structure;

        $attr = $get_property_value(
            $structure->attr,
            [
                "if_closure" => function ( $attr ) use ( $that ) { return SmartUtil::run_callback( $attr, [ $that ] ); }, //if user passes a closure, pass those optional parameters that they can use
                "if_other" => function ( $attr ) { return $attr; }, //just directly return the string for this type of structure item
                "if_array" => function ( $attr ) {
                    $props = array_map( function ( $attr, $attr_value ) { //build attribute values from passed array
                        return $attr . ' = "' . $attr_value . '"';
                    }, array_keys( $attr ), $attr );

                    return implode( ' ', $props );
                },
            ]
        );

        $options_map = $this->_options_map;

        $options = $get_property_value(
            $structure->options,
            [
                "if_closure" => function ( $options ) use ( $that ) { return SmartUtil::run_callback( $options, [ $that ] ); },
                "if_other" => function ( $options ) { return $options; },
                "if_array" => function ( $options ) use ( $that, $options_map ) {
                    $props = array_map( function ( $option, $value ) use ( $that, $options_map ) {
                        if ( is_bool( $value ) )
                        {
                            $str_val = var_export( $value, true );
                            if ( isset( $options_map[ $option ] ) )
                            {
                                if ( $value !== $options_map[ $option ] )
                                {
                                    return 'data-widget-' . $option . '="' . $str_val . '"';
                                } else return '';
                            } else return 'data-widget-' . $option . '="' . $str_val . '"';
                        }
                        return 'data-widget-' . $option . '="' . $value . '"';
                    }, array_keys( $options ), $options );

                    return implode( ' ', $props );
                },
            ]
        );

        $body = $get_property_value(
            $structure->body,
            [
                "if_closure" => function ( $body ) use ( $that ) { return SmartUtil::run_callback( $body, [ $that ] ); },
                "if_other" => function ( $body ) {
                    return '<div class="widget-body">' . $body . '</div>';
                },
                "if_array" => function ( $body ) use ( $that ) {
                    $editbox = '';
                    if ( isset( $body[ "editbox" ] ) )
                    {
                        $editbox = '<div class="jarviswidget-editbox">';
                        $editbox .= $body[ "editbox" ];
                        $editbox .= '</div>';
                    }

                    $content = '';
                    if ( isset( $body[ 'content' ] ) )
                    {
                        if ( SmartUtil::is_closure( $body[ 'content' ] ) )
                        {
                            $content = SmartUtil::run_callback( $body[ 'content' ], [ $that ] );
                        } else
                        {
                            $content = $body[ 'content' ];
                        }
                    }

                    $class = 'widget-body';
                    if ( isset( $body[ "class" ] ) )
                    {
                        if ( is_array( $body[ "class" ] ) )
                        {
                            $class .= ' ' . implode( ' ', $body[ "class" ] );
                        } else
                        {
                            $class .= ' ' . $body[ "class" ];
                        }
                    }

                    $toolbar = '';
                    if ( isset( $body[ "toolbar" ] ) )
                    {
                        $toolbar = '<div class="widget-body-toolbar">';
                        $toolbar .= $body[ "toolbar" ];
                        $toolbar .= '</div>';
                    }

                    $footer = '';
                    if ( isset( $body[ 'footer' ] ) )
                    {
                        $footer = '<div class="widget-footer">';
                        $footer .= $body[ 'footer' ];
                        $footer .= '</div>';
                    }

                    $result = $editbox;
                    $result .= '<div class="' . $class . '">';
                    $result .= $toolbar;
                    $result .= $content;
                    $result .= $footer;
                    $result .= '</div>';

                    return $result;
                },
            ]
        );

        $header = $get_property_value(
            $structure->header,
            [
                "if_closure" => function ( $header ) use ( $that ) { return SmartUtil::run_callback( $body, [ $that ] ); },
                "if_other" => function ( $body ) { return $body; },
                "if_array" => function ( $body ) use ( $get_property_value, $that ) {
                    $toolbar_htm = '';

                    if ( isset( $body[ "icon" ] ) )
                    {
                        $toolbar_htm .= '<span class="widget-icon"> <i class="' . SmartUI::$icon_source . ' ' . $body[ "icon" ] . '"></i> </span>';
                    }

                    if ( isset( $body[ "toolbar" ] ) )
                    {
                        $toolbar_htm .= $get_property_value( $body[ "toolbar" ], [
                            "if_closure" => function ( $toolbar ) use ( $that ) { return SmartUtil::run_callback( $toolbar, [ $that, $toolbar ] ); },
                            "if_other" => function ( $toolbar ) { return $toolbar; },
                            "if_array" => function ( $toolbar ) {
                                $toolbar_props_htm = [];
                                foreach ( $toolbar as $toolbar_prop )
                                {
                                    $id      = '';
                                    $class   = 'widget-toolbar';
                                    $attrs   = [];
                                    $content = '';
                                    if ( is_string( $toolbar_prop ) )
                                    {
                                        $content = $toolbar_prop;
                                    } else if ( is_array( $toolbar_prop ) )
                                    {
                                        $id    = isset( $toolbar_prop[ "id" ] ) ? $toolbar_prop[ "id" ] : '';
                                        $class .= isset( $toolbar_prop[ "class" ] ) ? ' ' . $toolbar_prop[ "class" ] : '';
                                        if ( isset( $toolbar_prop[ "attr" ] ) )
                                        {
                                            if ( is_array( $toolbar_prop[ "attr" ] ) )
                                            {
                                                foreach ( $toolbar_prop[ "attr" ] as $attr => $value )
                                                {
                                                    $attrs[] = $attr . '="' . $value . '"';
                                                }
                                            } else
                                            {
                                                $attrs[] = $toolbar_prop[ "attr" ];
                                            }
                                        }
                                        $content = isset( $toolbar_prop[ "content" ] ) ? $toolbar_prop[ "content" ] : '';
                                    }

                                    $htm = '<div class="' . trim( $class ) . '" id="' . $id . '" ' . implode( ' ', $attrs ) . '>';
                                    $htm .= $content;
                                    $htm .= '</div>';

                                    $toolbar_props_htm[] = $htm;
                                }

                                return implode( ' ', $toolbar_props_htm );
                            },
                        ] );
                    }

                    if ( isset( $body[ "title" ] ) )
                    {
                        $toolbar_htm .= $body[ "title" ];
                    } else
                        $toolbar_htm .= '<h2><code>SmartUI::Widget->header[content] not defined</code></h2>';

                    return $toolbar_htm;
                },
            ]
        );

        $class = $get_property_value( $structure->class, [
            "if_closure" => function ( $class ) use ( $that ) { return SmartUtil::run_callback( $class, [ $that ] ); },
            "if_array" => function ( $class ) {
                return implode( ' ', $class );
            },
        ] );

        $color = $get_property_value(
            $structure->color,
            [
                "if_closure" => function ( $color ) use ( $that ) { return SmartUtil::run_callback( $color, [ $that ] ); },
                "if_other" => function ( $color ) { return $color ? 'jarviswidget-color-' . $color : ''; },
                "if_array" => function ( $color ) {
                    SmartUI::err( 'SmartUI::Widget::color requires string' );
                },
            ]
        );

        $id = $get_property_value(
            $structure->id,
            [
                "if_closure" => function ( $id ) use ( $that ) { return SmartUtil::run_callback( $id, [ $that ] ); },
                "if_array" => function ( $id ) {
                    SmartUI::err( 'SmartUI::Widget::id requires string.' );
                    return '';
                },
            ]
        );

        $color_classes = [ '', 'jarviswidget-color-magenta', 'jarviswidget-color-greenLight', 'jarviswidget-color-blue', 'jarviswidget-color-red' ];

        $id              = $id ? 'id="' . $id . '"' : '';
        $main_classes    = [ 'jarviswidget ' . $color_classes[ array_rand( $color_classes, 1 ) ], $color, $class ];
        $main_attributes = [ 'class="' . trim( implode( ' ', $main_classes ) ) . '"', $id, $options, $attr ];

        $result = '<div ' . trim( implode( ' ', $main_attributes ) ) . '>';
        $result .= '<header>' . $header . '</header>';
        $result .= '<div>' . $body . '</div>';
        $result .= '</div>';

        if ( $return ) return $result;
        else echo $result;
    }
}

?>
