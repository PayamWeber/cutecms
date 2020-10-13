<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use SmartUI;

class FormHelper
{
    /**
     * @var $form_attributes
     *                      form html attributes goes here
     */
    public $form_attributes = [];

    /**
     * @var $title
     *            form title goes here
     */
    public $title;

    /**
     * @var $color
     *            form header color goes here
     */
    public $color;

    /**
     * @var $id
     *            form id goes here
     */
    public $id;

    /**
     * @var $submit_text
     *                  submit button text goes here
     */
    public $submit_text;

    /**
     * @var $fields
     *             fields in form goes here as array
     */
    public $fields = [];

    /**
     * @var $fieldset
     *               form fields row set goes here
     */
    public $fieldset = [];

    /**
     * @var $footer_buttons
     *                     footer buttons goes here as array
     */
    public $footer_buttons = [];

    /**
     * @var $form
     *           form object
     */
    protected $form;

    /**
     * @var $smart_ui
     *           SmartUI object
     */
    protected $smart_ui;

    public function __construct()
    {
    }

    public function make( $print_html = false )
    {
        $default_args = [
            'form_options' => [],
            'title' => 'افزودن یک دسترسی',
            'submit_text' => 'انتشار',
            'color' => 'primary',
            'id' => Str::random(),
            'footer_buttons' => '',
        ];
        $args         = pmw_parse_args( $default_args, [
            'form_options' => $this->form_attributes,
            'title' => $this->title,
            'submit_text' => $this->submit_text,
            'color' => $this->color,
            'id' => $this->id,
            'footer_buttons' => $this->footer_buttons,
        ] );

        $default_options = [
            'in_widget' => true,
            'wrapper' => 'form',
            'token' => true,
            'method' => 'POST',
            'action' => '',
            'has_footer' => true,
        ];
        $options         = pmw_parse_args( $default_options, $args[ 'form_options' ] );

        $this->smart_ui = new SmartUI;
        $this->smart_ui->start_track();

        $this->form = $this->smart_ui->create_smartform( $this->fields );
        $this->form->title( $args[ 'title' ] );
        $this->form->color( $args[ 'color' ] );
        $this->form->id( $args[ 'id' ] );

        /**
         * set options
         */
        if ( $options ) {
            foreach ( $options as $option_key => $option_value ) {
                $this->form->options( $option_key, $option_value );
            }
        }

        /**
         * set fieldsets
         */
        if ( $this->fieldset ) {
            foreach ( $this->fieldset as $key => $fieldset ) {
                $this->form->fieldset( $key, $fieldset );
            }
        }

        $this->make_form_footer( $args );

        $result = $this->form->print_html( $print_html ? false : true );

        return $result;
    }

    protected function make_form_footer( $args = [] )
    {
        $this->form->footer( function () use ( $args ) {
            $html = '';
            if ( $args[ 'footer_buttons' ] && is_array( $args[ 'footer_buttons' ] ) ) {
                foreach ( $args[ 'footer_buttons' ] as $button ) {
                    $button[ 'attr' ]      = $button[ 'attr' ] ?? '';
                    $button[ 'attr_text' ] = '';
                    $button[ 'title' ]     = $button[ 'title' ] ?? '';
                    $button[ 'color' ]     = $button[ 'color' ] ?? 'primary';

                    // generate attributes
                    if ( $button[ 'attr' ] ) {
                        foreach ( $button[ 'attr' ] as $attr_key => $attr_value ) {
                            $button[ 'attr_text' ] .= " $attr_key='$attr_value' ";
                        }
                    }

                    $button = (object) $button;
                    $html   .= "<a class=\"btn btn-$button->color btn-glow \" $button->attr_text>$button->title</a>";
                }
            }
            $html .= $this->smart_ui->create_button( $args[ 'submit_text' ], 'primary btn-glow ml-1' )->attr( [ 'type' => 'submit' ] )->print_html( true );
            return $html;
        } );
    }
}
