<?php

class SmartForm extends SmartUI
{

	const FORM_FIELD_INPUT       = 'input';
	const FORM_FIELD_TEXTAREA    = 'textarea';
	const FORM_FIELD_CHECKBOX    = 'checkbox';
	const FORM_FIELD_RADIO       = 'radio';
	const FORM_FIELD_SELECT      = 'select';
	const FORM_FIELD_SELECT2     = 'select2';
	const FORM_FIELD_MULTISELECT = 'multi-select';
	const FORM_FIELD_RATING      = 'rating';
	const FORM_FIELD_RATINGS     = 'ratings';
	const FORM_FIELD_FILEINPUT   = 'file-input';
	const FORM_FIELD_LABEL       = 'label';
	const FORM_FIELD_HIDDEN      = 'hidden';
	const FORM_FIELD_BLANK       = 'blank';
	const FORM_FIELD_HTML        = 'html';

	private $_options_map = [
		'in_widget' => true,
		'wrapper' => 'form',
		'token' => true,
		'method' => 'POST',
		'action' => '',
		'has_footer' => true,
	];

	private $_structure = [
		'field' => [],
		'fieldset' => [],
		'type' => [],
		'property' => [],
		'footer' => '',
		'header' => '',
		'widget' => null,
		'title' => '',
		'color' => 'primary',
		'col' => [],
		'options' => [],
		'attr' => [],
		'id' => '',
		'class' => [],
	];

	public function __construct( $fields, $options = [] )
	{
		$this->_init_structure( $fields, $options );
	}

	private function _init_structure( $fields, $user_options = [] )
	{
		$this->_structure          = parent::array_to_object( $this->_structure );
		$this->_structure->options = parent::set_array_prop_def( $this->_options_map, $user_options );
		$this->_structure->field   = $fields;
		$ui                        = new parent();
		$widget                    = $ui->create_widget();
		$widget->options( 'editbutton', false )
			->body( 'class', 'no-padding' )
			->header( 'title', '<h2></h2>' );

		$this->_structure->widget = $widget;
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

	private function _create_field_group( $collumed, $field_html, $col )
	{
		$group            = new stdClass;
		$group->collumned = $collumed;
		$group->items     = [ $field_html ];
		$group->total     = (int) $col;
		return $group;
	}

	public function print_html( $return = false )
	{
		$get_property_value = parent::_get_property_value_func();

		$that      = $this;
		$structure = $this->_structure;

		$fields = $get_property_value( $structure->field, [
			'if_closure' => function ( $fields ) use ( $that ) {
				return SmartUI::run_callback( $fields, [ $that ] );
			},
			'if_other' => function ( $fields ) {
				SmartUI::err( 'SmartUI::SmartForm:field requires array' );
				return null;
			},
		] );

		if ( ! is_array( $fields ) )
		{
			parent::err( "SmartUI::SmartForm:field requires array" );
			return null;
		}

		if ( ! $structure->fieldset || ! is_array( $structure->fieldset ) )
		{
			$structure->fieldset[] = array_keys( $fields );
		}

		$fieldsets_html_list = [];
		$hidden_fields_list  = [];
		$fieldsets           = count( $structure->fieldset );
		for ( $fs_index = 0; $fs_index < $fieldsets; $fs_index++ )
		{
			$fs_fields = $structure->fieldset[ $fs_index ];

			$groups        = [];
			$with_col_cntr = 0;
			$grouped       = false;

			if ( is_string( $fs_fields ) ) $fs_fields = [ $fs_fields ];

			foreach ( $fs_fields as $field_name )
			{
				$field      = $structure->field[ $field_name ];
				$field_prop = [
					'type' => self::FORM_FIELD_INPUT,
					'col' => 0,
					'properties' => [],
				];

				$new_field_prop = parent::get_clean_structure( $field_prop, $field, [ $this, $fs_index, $fs_fields ], 'type' );

				if ( $new_field_prop[ 'type' ] == self::FORM_FIELD_HIDDEN )
				{
					$field_html           = self::print_field( $field_name, self::FORM_FIELD_HIDDEN, $new_field_prop[ 'properties' ], true );
					$hidden_fields_list[] = $field_html;
					continue;
				}

				if ( isset( $structure->property[ $field_name ] ) ) $new_field_prop[ 'properties' ] = $structure->property[ $field_name ];
				if ( isset( $structure->col[ $field_name ] ) ) $new_field_prop[ 'col' ] = $structure->col[ $field_name ];
				if ( isset( $structure->type[ $field_name ] ) ) $new_field_prop[ 'type' ] = $structure->type[ $field_name ];

				$field_html = self::print_field( $field_name, $new_field_prop[ 'type' ], $new_field_prop[ 'properties' ], $new_field_prop[ 'col' ], true );

				$collumned = $new_field_prop[ 'col' ] > 0 && $new_field_prop[ 'col' ] < 12;

				if ( ! $grouped )
				{
					$last_group_key            = SmartUtil::create_id();
					$group                     = self::_create_field_group( $collumned, $field_html, $new_field_prop[ 'col' ] );
					$groups[ $last_group_key ] = $group;
					$grouped                   = true;
				} else
				{
					$last_group = $groups[ $last_group_key ];
					if ( $last_group->collumned === $collumned && $last_group->total < 12 )
					{
						$last_group->items[] = $field_html;
						$last_group->total   = $last_group->total + (int) $new_field_prop[ 'col' ];
					} else
					{
						$last_group_key            = SmartUtil::create_id();
						$group                     = self::_create_field_group( $collumned, $field_html, $new_field_prop[ 'col' ] );
						$groups[ $last_group_key ] = $group;
						$grouped                   = true;
					}
				}
			}

			$fields_html_list = [];

			foreach ( $groups as $group )
			{
				$group_html  = '';
				$fields_html = implode( '', $group->items );
				if ( $group->collumned )
				{
					$group_html .= '<div class="row">';
					$group_html .= $fields_html;
					$group_html .= '</div>';
				} else
				{
					$group_html .= $fields_html;
				}

				$fields_html_list[] = $group_html;
			}

			$fieldsets_html_list[] = '<fieldset>' . implode( '', $fields_html_list ) . '</fieldset>';
		}

		$header = $get_property_value( $structure->header, [
			'if_closure' => function ( $header ) use ( $that ) {
				return SmartUI::run_callback( $header, [ $that ] );
			},
		] );

		$footer = $get_property_value( $structure->footer, [
			'if_closure' => function ( $footer ) use ( $that ) {
				return SmartUI::run_callback( $footer, [ $that ] );
			},
		] );

		$form_attrs   = [];
		$form_attrs[] = 'class="smart-form ' . ( is_array( $structure->class ) ? implode( ' ', $structure->class ) : $structure->class ) . '"';
		$form_attrs[] = 'id="' . ( $structure->id ? $structure->id : SmartUtil::create_id() ) . '"';

		$form_attrs = array_merge( $form_attrs, array_map( function ( $attr, $value ) {
			return $attr . '="' . $value . '"';
		}, array_keys( $structure->attr ), $structure->attr ) );

		if ( $structure->options[ 'wrapper' ] )
		{
			$form_html = '<' . $structure->options[ 'wrapper' ] . ' ' . implode( ' ', $form_attrs ) . '
				method="' . $structure->options[ 'method' ] . '" action="' . $structure->options[ 'action' ] . '">';
		} else
		{
			$form_html = '';
		}
		if ( $structure->options[ 'token' ] )
		{
			$form_html .= "<input name='_token' type='hidden' value='" . csrf_token() . "'>";
		}
		$form_html .= $header ? '<header>' . $header . '</header>' : '';
		$form_html .= implode( '', $fieldsets_html_list );
		$form_html .= implode( ' ', $hidden_fields_list );
		if ( $structure->options[ 'has_footer' ] )
		{
			$form_html .= $footer ? '<div class="form-actions right">' . $footer . '</div>' : '';
		}
		if ( $structure->options[ 'wrapper' ] )
		{
			$form_html .= '</' . $structure->options[ 'wrapper' ] . '>';
		}

		if ( isset( $structure->options[ "in_widget" ] ) && $structure->options[ "in_widget" ] )
		{
			$structure->widget->body( "content", $form_html );
            $structure->widget->color( $structure->color );
            $structure->widget->id( $structure->id );
			if ( $structure->title )
			{
				$structure->widget->header( 'title', $structure->title );
			}

			$result = $structure->widget->print_html( true );
		} else $result = $form_html;

		if ( $return ) return $result;
		else echo $result;
	}

	public static function print_field( $name, $type = self::FORM_FIELD_INPUT, $properties = [], $col = false, $return = false )
	{
		$field_html = self::_get_field_html( $name, $type, $properties );
		if ( $type == self::FORM_FIELD_HIDDEN ) return $field_html;

		$classes = [];
		if ( $col && $col < 12 ) $classes[] = 'col col-' . $col;
		if ( $type == self::FORM_FIELD_LABEL ) $classes[] = 'label';

		$result = '<section' . ( $classes ? ' class="' . implode( ' ', $classes ) . '"' : '' ) . '>';
		$result .= "<div class='form-group'>";
		$result .= $field_html;
		$result .= '</section>';

		if ( $return ) return $result;
		else echo $result;
	}

	private static function _get_field_html( $name, $field_type = self::FORM_FIELD_INPUT, $properties = [], $field_html_only = false )
	{
		$field_class_map = [
			self::FORM_FIELD_INPUT => 'input',
			self::FORM_FIELD_FILEINPUT => 'input input-file',
			self::FORM_FIELD_SELECT => 'select',
			self::FORM_FIELD_SELECT2 => 'select',
			self::FORM_FIELD_MULTISELECT => 'select select-multiple',
			self::FORM_FIELD_TEXTAREA => 'textarea',
			self::FORM_FIELD_CHECKBOX => 'checkbox',
			self::FORM_FIELD_RADIO => 'radio',
			self::FORM_FIELD_RATING => 'rating',
			self::FORM_FIELD_RATINGS => 'rating',
			self::FORM_FIELD_HIDDEN => '',
			self::FORM_FIELD_BLANK => '',
		];

		$result      = '';
		$field_html  = '';
		$result_html = '';
		$notes       = '';
		$label       = '';
		$attrs       = [];
		switch ( $field_type )
		{
			case self::FORM_FIELD_LABEL:
				$default_prop = [
					'label' => '',
				];

				$new_prop = parent::get_clean_structure( $default_prop, $properties, [], 'label' );
//                $result_html = $new_prop[ 'label' ];
				break;
			case self::FORM_FIELD_BLANK:
				$default_prop = [
					'content' => '',
				];

				$new_prop    = parent::get_clean_structure( $default_prop, $properties, [], 'content' );
				$result_html = $new_prop[ 'content' ];
				break;
			case self::FORM_FIELD_HTML:
				$default_prop = [
					'html' => '',
				];

				$new_prop    = parent::get_clean_structure( $default_prop, $properties, [], 'html' );
				$result_html = $new_prop[ 'html' ];
				break;
			case self::FORM_FIELD_RATINGS:
				$default_prop = [
					'items' => [],
					'icon' => SmartUI::$icon_source . '-star',
				];

				$new_prop = parent::get_clean_structure( $default_prop, $properties, [], 'max' );
				if ( ! is_array( $new_prop[ 'items' ] ) ) $new_prop[ 'items' ] = [ $new_prop[ 'items' ] ];
				$items            = $new_prop[ 'items' ];
				$rating_html_list = [];
				foreach ( $items as $item )
				{
					$item_prop = [
						'max' => 5,
						'icon' => $new_prop[ 'icon' ],
						'name' => $name . '-' . SmartUtil::create_id(),
						'label' => '',
					];

					$new_item_prop = parent::set_array_prop_def( $item_prop, $item, 'max' );
					$field_html    = self::_get_field_html( $new_item_prop[ 'name' ], self::FORM_FIELD_RATING, $new_item_prop, true );
					$field_html    .= $new_item_prop[ 'label' ] ? $new_item_prop[ 'label' ] : '&nbsp;';

					$result_html        = '	<div class="' . $field_class_map[ $field_type ] . '">';
					$result_html        .= $field_html;
					$result_html        .= '	</div>';
					$rating_html_list[] = $result_html;
				}

				$result_html = implode( '', $rating_html_list );
				break;
			case self::FORM_FIELD_RATING:
				$default_prop = [
					'max' => 5,
					'icon' => SmartUI::$icon_source . '-star',
				];

				$new_prop = parent::get_clean_structure( $default_prop, $properties, [], 'max' );

				$rating_html_list = [];
				for ( $i = $new_prop[ 'max' ]; $i >= 1; $i-- )
				{
					$rate_id     = $name . '-' . $i;
					$rating_html = self::_get_field_html( $name, self::FORM_FIELD_INPUT, [
						'type' => 'radio',
						'id' => $rate_id,
					], true );

					$rating_html        .= '<label for="' . $rate_id . '"><i class="' . SmartUI::$icon_source . ' ' . $new_prop[ 'icon' ] . '"></i></label>';
					$rating_html_list[] = $rating_html;
				}

				$field_html .= implode( '', $rating_html_list );
				if ( $field_html_only ) return $field_html;

				$result_html .= '	<label class="' . $field_class_map[ $field_type ] . '">';
				$result_html .= $field_html;
				$result_html .= '	</label>';

				break;
			case self::FORM_FIELD_TEXTAREA:
				$default_prop = [
					'rows' => 3,
					'attr' => [],
					'class' => [],
					'icon' => '',
					'icon_append' => true,
					'value' => '',
					'id' => '',
					'type' => '',
					'placeholder' => '',
					'disabled' => false,
					'wrapper' => 'label',
				];
				$new_prop     = parent::get_clean_structure( $default_prop, $properties, [], 'placeholder' );

				$classes   = [];
				$classes[] = 'custom-scroll';
				$classes[] = 'form-control';
				$classes[] = $field_class_map[ $field_type ];
				if ( $new_prop[ 'class' ] ) array_push( $classes, $new_prop[ 'class' ] );

				$attrs   = [];
				$attrs[] = 'class="' . implode( ' ', $classes ) . '"';
				$attrs[] = 'rows="' . $new_prop[ 'rows' ] . '"';
				$attrs[] = 'name="' . $name . '"';
				if ( $new_prop[ 'disabled' ] ) $attrs[] = 'disabled="disabled"';
				if ( $new_prop[ 'id' ] ) $attrs[] = 'id="' . $new_prop[ 'id' ] . '"';
				if ( $new_prop[ 'placeholder' ] ) $attrs[] = 'placeholder="' . $new_prop[ 'placeholder' ] . '"';
				if ( $new_prop[ 'attr' ] ) $attrs[] = implode( ' ', $new_prop[ 'attr' ] );

				if ( $new_prop[ 'icon' ] )
					$field_html .= '<i class="icon-' . ( $new_prop[ 'icon_append' ] ? 'append' : 'prepend' ) . ' ' . SmartUI::$icon_source . ' ' . $new_prop[ 'icon' ] . '"></i>';

				$field_html .= '<textarea ' . implode( ' ', $attrs ) . '>';
				$field_html .= $new_prop[ 'value' ];
				$field_html .= '</textarea>';

				$field_class_map[ self::FORM_FIELD_TEXTAREA ] = 'textarea' . ( $new_prop[ 'type' ] ? ' textarea-' . $new_prop[ 'type' ] : '' );

				if ( $field_html_only ) return $field_html;

				$result_html .= $field_html;

				break;
			case self::FORM_FIELD_MULTISELECT:
				if ( isset( $properties[ 'attr' ] ) )
				{
					array_push( $properties[ 'attr' ], [ 'multiple="multiple"', 'class="custom-scroll"' ] );
				} else
				{
					$properties[ 'attr' ] = [ 'multiple="multiple"' ];
				}
				if ( isset( $properties[ 'class' ] ) ) array_push( $properties[ 'class' ], [ 'custom-scroll' ] );
				else $properties[ 'class' ] = [ 'custom-scroll' ];

				$properties[ 'icon' ] = '';

				$field_html = self::_get_field_html( $name, self::FORM_FIELD_SELECT, $properties, true );

				if ( $field_html_only ) return $field_html;

				$result_html .= $field_html;
				break;
			case self::FORM_FIELD_SELECT2:
				if ( ! isset( $properties[ 'class' ] ) ) $properties[ 'class' ] = '';
				if ( ! is_array( $properties[ 'class' ] ) ) $properties[ 'class' ] = [ $properties[ 'class' ] ];
				array_push( $properties[ 'class' ], 'select2' );

				$properties[ 'icon' ] = '';

				$field_html = self::_get_field_html( $name, self::FORM_FIELD_SELECT, $properties, true );

				if ( $field_html_only ) return $field_html;

				$result_html .= $field_html;
				break;
			case self::FORM_FIELD_SELECT:
				$default_prop = [
					'data' => [],
					'display' => '',
					'value' => '',
					'container' => 'select',
					'selected' => false,
					'id' => '',
					'attr' => [],
					'class' => [],
					'icon' => '<i></i>',
					'item_attr' => null,
					'disabled' => false,
				];

				$new_prop = parent::get_clean_structure( $default_prop, $properties, [], 'data' );
				$data     = $new_prop[ 'data' ];
				if ( ! is_array( $data ) )
				{
					parent::err( 'SmartUI::Form "data" is required for "select" field.' );
					return '';
				}

				if ( ! $data ) $data = [ [ 'No Data' ] ];

				$data = SmartUtil::object_to_array( $data );

				$option_list = [];
				foreach ( $data as $row_key => $row )
				{
					$selected = $row_key == $new_prop[ 'selected' ];

					$option_list[] = '<option value="' . $row_key . '"' . ( $selected ? ' selected' : '' ) . '>' . $row . '</option>';
				}
				$new_prop[ 'class' ][] = 'form-control';

				$attrs   = [];
				$attrs[] = 'name="' . $name . '"';
				if ( $new_prop[ 'disabled' ] ) $attrs[] = 'disabled="disabled"';
				if ( $new_prop[ 'id' ] ) $attrs[] = 'id="' . $new_prop[ 'id' ] . '"';
				if ( $new_prop[ 'attr' ] ) $attrs[] = implode( ' ', $new_prop[ 'attr' ] );
				if ( $new_prop[ 'class' ] ) $attrs[] = 'class="' . implode( ' ', $new_prop[ 'class' ] ) . '"';

				$field_html = '<' . $new_prop[ 'container' ] . ' ' . implode( ' ', $attrs ) . '>';
				$field_html .= implode( '', $option_list );
				$field_html .= '</' . $new_prop[ 'container' ] . '>' . $new_prop[ 'icon' ];

				if ( $field_html_only ) return $field_html;

				$result_html .= $field_html;
				break;
			case self::FORM_FIELD_FILEINPUT:
				$file_button = self::_get_field_html( $name, self::FORM_FIELD_INPUT, [
					'type' => 'file',
					'attr' => array_merge( [ 'onchange="this.parentNode.nextSibling.value = this.value"' ], isset( $properties[ 'attr' ] ) ? $properties[ 'attr' ] : [] ),
				], true );
				$field_html  = '<span class="button">';
				$field_html  .= $file_button;
				$field_html  .= 'Browse</span>';

				$default_prop = [
					'icon' => false,
					'tooltip' => false,
					'attr' => [ 'readonly' ],
					'type' => 'text',
				];
				if ( $properties )
				{
					foreach ( $properties as $key => $value )
					{
						if ( ! isset( $default_prop[ $key ] ) )
						{
							$default_prop[ $key ] = $value;
						}
					}
				}

				$field_html .= self::_get_field_html( $name . '-display', self::FORM_FIELD_INPUT, $default_prop, true );

				if ( $field_html_only ) return $field_html;

				$result_html .= $field_html;
				break;
			case self::FORM_FIELD_HIDDEN:
				$default_prop = [
					'icon' => false,
					'tooltip' => false,
					'type' => 'hidden',
					'value' => '',
				];

				$new_prop = parent::get_clean_structure( $default_prop, $properties, [], 'value' );

				$field_html .= self::_get_field_html( $name, self::FORM_FIELD_INPUT, $new_prop, true );
				return $field_html;
				break;
			case self::FORM_FIELD_INPUT:
				$default_prop = [
					'type' => 'text',
					'attr' => [],
					'id' => '',
					'icon' => '',
					'icon_append' => true,
					'placeholder' => '',
					'value' => '',
					'tooltip' => [],
					'disabled' => false,
					'autocomplete' => false,
					'size' => '',
					'class' => [],
				];

				$new_prop = parent::get_clean_structure( $default_prop, $properties, [], 'placeholder' );

				$classes   = [];
				$classes[] = $field_class_map[ $field_type ];
				$classes[] = 'form-control';
				if ( $new_prop[ 'class' ] ) array_push( $classes, $new_prop[ 'class' ] );
				if ( $new_prop[ 'size' ] ) $classes[] = 'input-' . $new_prop[ 'size' ];
				$attrs   = [];
				$attrs[] = $classes ? 'class="' . implode( ' ', $classes ) . '"' : '';
				$attrs[] = 'type="' . $new_prop[ 'type' ] . '"';
				$attrs[] = 'name="' . $name . '"';
				if ( $new_prop[ 'attr' ] ) $attrs[] = implode( ' ', $new_prop[ 'attr' ] );
				if ( $new_prop[ 'id' ] ) $attrs[] = 'id="' . $new_prop[ 'id' ] . '"';
				$attrs[] = 'value="' . $new_prop[ 'value' ] . '"';
				if ( $new_prop[ 'placeholder' ] ) $attrs[] = 'placeholder="' . $new_prop[ 'placeholder' ] . '"';
				if ( $new_prop[ 'disabled' ] ) $attrs[] = 'disabled="disabled"';
				$ac_html = '';
				if ( $new_prop[ 'autocomplete' ] )
				{
					$ac_prop = [
						'data' => [],
						'display' => '',
						'value' => '',
					];
					if ( ! isset( $new_prop[ 'autocomplete' ][ 'data' ] ) )
						$ac_prop[ 'data' ] = $new_prop[ 'autocomplete' ];
					else
					{
						$ac_prop[ 'data' ]    = $new_prop[ 'autocomplete' ][ 'data' ];
						$ac_prop[ 'display' ] = isset( $new_prop[ 'autocomplete' ][ 'display' ] ) ? $new_prop[ 'autocomplete' ][ 'display' ] : '';
						$ac_prop[ 'value' ]   = isset( $new_prop[ 'autocomplete' ][ 'value' ] ) ? $new_prop[ 'autocomplete' ][ 'value' ] : '';
					}

					$list_name = 'list-' . $name . '-' . SmartUtil::create_id();
					$ac_html   = self::_get_field_html( '', self::FORM_FIELD_SELECT, [
						'container' => 'datalist',
						'data' => $ac_prop[ 'data' ],
						'display' => $ac_prop[ 'display' ],
						'value' => $ac_prop[ 'value' ],
						'id' => $list_name,
					], true );
					$attrs[]   = 'list="' . $list_name . '"';
				}

//                if ( $new_prop[ 'icon' ] )
//                    $field_html .= '<i class="icon-' . ( $new_prop[ 'icon_append' ] ? 'append' : 'prepend' ) . ' ' . SmartUI::$icon_source . ' ' . $new_prop[ 'icon' ] . '"></i>';

				$field_html .= '<input ' . implode( ' ', $attrs ) . ' />';
				$field_html .= $ac_html;

				if ( $new_prop[ 'tooltip' ] )
				{
					$tooltip_prop = [
						'content' => '',
						'position' => 'top-right',
					];

					$new_tooltip_prop = parent::set_array_prop_def( $tooltip_prop, $new_prop[ 'tooltip' ], 'content' );
					$field_html       .= '<b class="tooltip tooltip-' . $new_tooltip_prop[ 'position' ] . '">' . $new_tooltip_prop[ 'content' ] . '</b>';
				}

				if ( $field_html_only ) return $field_html;

				$result_html .= $field_html;
				break;
			case self::FORM_FIELD_RADIO:
				$default_prop = [
					'items' => [],
					'cols' => 0,
					'inline' => false,
					'toggle' => false,
				];

				$new_prop = parent::get_clean_structure( $default_prop, $properties, [], 'items' );

				if ( ! is_array( $new_prop[ 'items' ] ) ) $new_prop[ 'items' ] = [ $new_prop[ 'items' ] ];

				$items          = $new_prop[ 'items' ];
				$item_list_html = [];
				foreach ( $items as $item )
				{
					$items_prop = [
						'name' => $name,
						'checked' => false,
						'value' => '',
						'label' => '',
						'id' => '',
						'disabled' => false,
					];

					$new_item_prop         = parent::set_array_prop_def( $items_prop, $item, 'label' );
					$unique_id             = uniqid();
					$new_item_prop[ 'id' ] = $new_item_prop[ 'id' ] ? $new_item_prop[ 'id' ] : $unique_id;

					$item_html = self::_get_field_html( $new_item_prop[ 'name' ], self::FORM_FIELD_INPUT, [
						'type' => 'radio',
						'class' => 'radio',
						'attr' => $new_item_prop[ 'checked' ] ? [ 'checked' ] : null,
						'value' => $new_item_prop[ 'value' ],
						'id' => $new_item_prop[ 'id' ],
					], true );

					if ( $new_prop[ 'toggle' ] )
					{
						$text_off  = is_array( $new_prop[ 'toggle' ] ) && isset( $new_prop[ 'toggle' ][ 'text-off' ] ) ? $new_prop[ 'toggle' ][ 'text-off' ] : 'OFF';
						$text_on   = is_array( $new_prop[ 'toggle' ] ) && isset( $new_prop[ 'toggle' ][ 'text-on' ] ) ? $new_prop[ 'toggle' ][ 'text-on' ] : 'ON';
						$item_html .= '<i data-swchon-text="' . $text_on . '" data-swchoff-text="' . $text_off . '"></i>';
					} else $item_html .= '<i></i>';

					$item_html .= $new_item_prop[ 'label' ];

					$field_html = '	<label class="' . ( $new_prop[ 'toggle' ] ? 'toggle' : $field_class_map[ $field_type ] ) . ' ' . ( $new_item_prop[ 'disabled' ] ? 'state-disabled' : '' ) . '">';
					$field_html .= $item_html;
					$field_html .= '	</label>';

					$field_html = "
                    <div class='skin skin-flat'>
                        <fieldset class='checkbox-fieldset'>
                            <label for=\"$new_item_prop[id]\">
                            $item_html
                            $new_item_prop[label]
                            </label>
                        </fieldset>
                    </div>
                    ";

					$item_list_html[] = $field_html;
				}

				if ( $new_prop[ 'cols' ] )
				{
					$result_html .= '<div class="row">';
					$result_html .= self::print_col_items( $item_list_html, function ( $item ) { return $item; }, $new_prop[ 'cols' ], true );
					$result_html .= '</div>';
				} else
				{
					$list_html = implode( '', $item_list_html );
					if ( $new_prop[ 'inline' ] )
					{
						$result_html .= '<div class="inline-group">';
						$result_html .= $list_html;
						$result_html .= '</div>';
					} else $result_html .= $list_html;
				}

				if ( $field_html_only ) return $result_html;

				break;
			case self::FORM_FIELD_CHECKBOX:
				$default_prop = [
					'items' => [],
					'cols' => 0,
					'inline' => false,
					'toggle' => false,
				];

				$new_prop = parent::get_clean_structure( $default_prop, $properties, [], 'items' );

				if ( ! is_array( $new_prop[ 'items' ] ) ) $new_prop[ 'items' ] = [ $new_prop[ 'items' ] ];

				$items          = $new_prop[ 'items' ];
				$item_list_html = [];
				foreach ( $items as $item )
				{
					$unique_id  = uniqid();
					$items_prop = [
						'name' => $name,
						'checked' => false,
						'value' => '',
						'label' => '',
						'id' => '',
						'disabled' => false,
					];

					$new_item_prop = parent::set_array_prop_def( $items_prop, $item, 'label' );

					$new_item_prop[ 'id' ] = $new_item_prop[ 'id' ] ? $new_item_prop[ 'id' ] : $unique_id;

					$item_html = self::_get_field_html( $new_item_prop[ 'name' ], self::FORM_FIELD_INPUT, [
						'type' => 'checkbox',
						'class' => $new_prop[ 'toggle' ] ? 'switchery' : 'checkbox',
						'attr' => $new_item_prop[ 'checked' ] ? [ 'checked' ] : null,
						'value' => $new_item_prop[ 'value' ],
						'id' => $new_item_prop[ 'id' ],
					], true );

					$field_html = '	<label class="' . ( $new_prop[ 'toggle' ] ? 'toggle' : $field_class_map[ $field_type ] ) . ' ' . ( $new_item_prop[ 'disabled' ] ? 'state-disabled' : '' ) . '">';
					$field_html .= $item_html;
					$field_html .= '	</label>';

					$margin_class = '';
					if ( $new_prop[ 'toggle' ] )
					{
						$margin_class = 'ml-1';
					}

					$field_html = "
                    <div class='skin skin-flat'>
                        <fieldset class='checkbox-fieldset'>
                            <label for=\"$new_item_prop[id]\">
                            $item_html
                            <span class='$margin_class'>$new_item_prop[label]</span>
                            </label>
                        </fieldset>
                    </div>
                    ";

					$item_list_html[] = $field_html;
				}

				if ( $new_prop[ 'cols' ] )
				{
					$result_html .= '<div class="row">';
					$result_html .= self::print_col_items( $item_list_html, function ( $item ) { return $item; }, $new_prop[ 'cols' ], true );
					$result_html .= '</div>';
				} else
				{
					$list_html = implode( '', $item_list_html );
					if ( $new_prop[ 'inline' ] )
					{
						$result_html .= '<div class="inline-group">';
						$result_html .= $list_html;
						$result_html .= '</div>';
					} else $result_html .= $list_html;
				}

				if ( $field_html_only ) return $result_html;

				break;
		}
		$result_html .= "</div>";

		if ( is_array( $properties ) )
		{
			$notes = isset( $properties[ 'note' ] ) ? '<div class="note">' . $properties[ 'note' ] . '</div>' : '';
			$label = isset( $properties[ 'label' ] ) && $properties[ 'label' ] ? '<label class="label">' . $properties[ 'label' ] . '</label>' : '';
		}
		$result .= $label;
		$result .= $result_html;
		$result .= $notes;

		return $result;
	}

	public static function print_col_items( $items, $closure_content, $columns = 1, $return = false )
	{
		$htm_result    = '';
		$arr_htm_items = [];
		$col_cntr      = 0;
		$row_cntr      = 0;
		$result_count  = 0;
		for ( $i = 0; $i < $columns; $i++ )
		{
			$arr_htm_items[ $i ] = "";
		}
		$htm_item = "";
		if ( $items )
		{
			$result_count = count( $items );
			foreach ( $items as $item_data )
			{
				if ( $row_cntr >= ( $result_count / $columns ) )
				{
					$col_cntr++;
					$row_cntr = 0;
					$htm_item = "";
				}
				$row_cntr++;

				$htm_item                   .= $closure_content( $item_data );
				$arr_htm_items[ $col_cntr ] = '<div class="col col-' . ( 12 / $columns ) . '">' . $htm_item . '</div>';
			}
			foreach ( $arr_htm_items as $item_content )
			{
				$htm_result .= $item_content;
			}
		}
		if ( $return ) return $htm_result;
		else echo $htm_result;
	}

}

?>
