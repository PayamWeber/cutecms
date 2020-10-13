<?php

class Nav extends SmartUI
{

	private $_structure = [
		'nav' => [],
		'id' => '',
	];

	public function __construct( $nav_items = [] )
	{
		$this->_init_structure( $nav_items );
	}

	private function _init_structure( $nav_items )
	{
		$this->_structure      = parent::array_to_object( $this->_structure );
		$this->_structure->nav = $nav_items;
		$this->_structure->id  = parent::create_id( true );
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

	public function print_html( $return = false )
	{
		$get_property_value = parent::_get_property_value_func();

		$that      = $this;
		$structure = $this->_structure;

		$nav_items = $get_property_value( $structure->nav, [
			'if_closure' => function ( $nav_items ) use ( $that ) {
				return SmartUI::run_callback( $nav_items, [ $that ] );
			},
			'if_other' => function ( $nav_items ) {
				SmartUI::err( 'SmartUI::Nav:nav requires array' );
				return null;
			},
		] );

		if ( ! is_array( $nav_items ) )
		{
			parent::err( "SmartUI::Nav:nav requires array" );
			return null;
		}

		$list_items = $this->parse_nav( $nav_items, true );
		$result     = parent::print_list( $list_items, [
			'type' => 'ul',
			'attr' => [],
			'class' => [
				'navigation navigation-main',
			],
			'id' => 'main-menu-navigation',
		], true );

		if ( $return ) return $result;
		else echo $result;
	}

	private function parse_nav( $nav_item, $is_parent = false )
	{
		if ( ! $nav_item ) return '';
		$nav_items_list = [];
		foreach ( $nav_item as $name => $nav )
		{
			$nav_prop = [
				'url' => '#',
				'url_target' => '',
				'icon' => '',
				'icon_badge' => '',
				'badge' => '',
				'label_htm' => '',
				'title' => $name,
				'title_append' => '',
				'label' => '',
				'active' => false,
				'sub' => [],
				'attr' => [],
				'class' => [],
				'li_class' => [],
				'capability' => '',
				'capability_operator' => 'OR',
			];

			$new_nav_prop = parent::set_array_prop_def( $nav_prop, $nav, 'title' );

			$new_nav_prop[ 'url' ]   = \Illuminate\Support\Facades\Route::has( $new_nav_prop[ 'url' ] ) ? route( $new_nav_prop[ 'url' ] ) : $new_nav_prop[ 'url' ];
			$new_nav_prop[ 'title' ] = lang( $new_nav_prop[ 'title' ] );

//          check capability added by pmw
			if ( $new_nav_prop[ 'capability' ] && ! is_user_can( $new_nav_prop[ 'capability' ], $new_nav_prop[ 'capability_operator' ] ) )
				continue;
			if ( $new_nav_prop[ 'sub' ] && ! $this->check_subs_capabilities( $new_nav_prop[ 'sub' ] ) )
				continue;

			$cap_route = get_cap_route( $new_nav_prop[ 'capability' ] );
			global $current_route;

			$is_active = ( is_array( $cap_route ) ) ? in_array( $current_route, $cap_route ) : ( $current_route == $cap_route );
			if ( $is_active )
				$new_nav_prop[ 'active' ] = true;
			if ( ! $is_active && clean_url( $new_nav_prop[ 'url' ] ) == clean_url( get_current_url() ) )
				$new_nav_prop[ 'active' ] = true;

			$icon_badge_prop = [
				'content' => '',
				'class' => '',
			];

			$icon_badge_prop = parent::set_array_prop_def( $icon_badge_prop, $new_nav_prop[ 'icon_badge' ], 'content' );
			$badge           = '';
			if ( $icon_badge_prop[ 'content' ] )
			{
				$badge_class = $icon_badge_prop[ 'class' ] ? 'class="' . $icon_badge_prop[ 'class' ] . '"' : '';
				$badge       = '<em ' . $badge_class . '>' . $icon_badge_prop[ 'content' ] . '</em>';
			}

			$icon         = $new_nav_prop[ 'icon' ] ? '<i class="la ' . $new_nav_prop[ 'icon' ] . '">' . $badge . '</i>' : '';
			$title        = $new_nav_prop[ 'title' ];
			$title_append = $new_nav_prop[ 'title_append' ];

			$display_title = $title . ' ' . $title_append;

			$label_htm = $new_nav_prop[ 'label_htm' ] ? ' ' . $new_nav_prop[ 'label_htm' ] : '';

			$display_text = $is_parent ? '<span class="menu-item-parent">' . $display_title . '</span>' : $display_title;
			$display_text .= $label_htm;

			$attrs = array_map( function ( $attr, $value ) {
				return $attr . '="' . $value . '"';
			}, array_keys( $new_nav_prop[ 'attr' ] ), $new_nav_prop[ 'attr' ] );

			if ( $new_nav_prop[ 'class' ] ) $attrs[] = 'class="' . $new_nav_prop[ 'class' ] . '"';

			if ( $new_nav_prop[ 'badge' ] && is_array( $new_nav_prop[ 'badge' ] ) )
			{
				$new_nav_prop[ 'badge' ] = "<span class='badge badge badge-info badge-pill float-right mr-2' 
                style='background-color: " . ( isset( $new_nav_prop[ 'badge' ][ 'color' ] ) ? $new_nav_prop[ 'badge' ][ 'color' ] : '' ) . "'>" .
					( isset( $new_nav_prop[ 'badge' ][ 'text' ] ) ? $new_nav_prop[ 'badge' ][ 'text' ] : '' ) . "</span>";
			} else if ( $new_nav_prop[ 'badge' ] && is_string( $new_nav_prop[ 'badge' ] ) )
			{
				$new_nav_prop[ 'badge' ] = "<span class='badge badge badge-info badge-pill float-right mr-2'>" . $new_nav_prop[ 'badge' ] . "</span>";
			} else
			{
				$new_nav_prop[ 'badge' ] = "";
			}

			$nav_htm = '<a
                href="' . $new_nav_prop[ 'url' ] . '"
                ' . ( $new_nav_prop[ 'url_target' ] ? 'target="' . $new_nav_prop[ 'url_target' ] . '"' : '' ) . '
                title="' . $title . '"
                ' . implode( ' ', $attrs ) . '>
                    ' . $icon . '
                    ' . $display_text . '
                    ' . $new_nav_prop[ 'label' ] . '
                    ' . $new_nav_prop[ 'badge' ] . '
                </a>';

			$li_classes = [];
			if ( $new_nav_prop[ "active" ] ) $li_classes[] = 'active';
			if ( ! $new_nav_prop[ 'sub' ] ) $li_classes[] = 'last_nav_child';
			if ( $new_nav_prop[ 'li_class' ] )
			{
				if ( is_string( $new_nav_prop[ 'li_class' ] ) ) $li_classes[] = $new_nav_prop[ 'li_class' ];
				else if ( is_array( $new_nav_prop[ 'li_class' ] ) ) $li_classes = array_merge( $li_classes, $new_nav_prop[ 'li_class' ] );
			}

			$nav_item_structure = [
				'content' => $nav_htm,
				'class' => implode( ' ', $li_classes ),
			];

			if ( isset( $new_nav_prop[ 'sub' ] ) )
			{
				if ( is_string( $new_nav_prop[ 'sub' ] ) )
				{
					$nav_item_structure[ 'subitems' ] = [ $new_nav_prop[ 'sub' ] ];
				} else
				{
					$nav_item_structure[ 'subitems' ] = $this->parse_nav( $new_nav_prop[ 'sub' ] );
				}
			}

			$nav_items_list[] = $nav_item_structure;
		}

		return $nav_items_list;
	}

	private function check_subs_capabilities( $subs )
	{
		$can = false;
		if ( $subs )
		{
			foreach ( $subs as $name => $sub )
			{
				$nav_prop     = [
					'url' => '#',
					'url_target' => '',
					'icon' => '',
					'icon_badge' => '',
					'label_htm' => '',
					'title' => $name,
					'title_append' => '',
					'label' => '',
					'active' => false,
					'sub' => [],
					'attr' => [],
					'class' => [],
					'li_class' => [],
					'capability' => '',
					'capability_operator' => 'OR',
				];
				$new_nav_prop = parent::set_array_prop_def( $nav_prop, $sub, 'title' );

				if ( $new_nav_prop[ 'capability' ] && is_user_can( $new_nav_prop[ 'capability' ], $new_nav_prop[ 'capability_operator' ] ) )
				{
					$can = true;
					break;
				} else if ( ! $new_nav_prop[ 'capability' ] )
				{
					$can = true;
					if ( $new_nav_prop[ 'sub' ] )
						$can = $this->check_subs_capabilities( $new_nav_prop[ 'sub' ] );
				} else
				{
					$can = false;
					if ( $new_nav_prop[ 'sub' ] )
						$can = $this->check_subs_capabilities( $new_nav_prop[ 'sub' ] );
				}
			}
		}
		return $can;
	}
}

?>
