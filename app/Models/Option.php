<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
	public $timestamps = FALSE;

	protected $table = 'options';

	protected $fillable = [
		'name',
		'value',
		'autoload',
	];

	/**
	 * this function return an option value by giving a name of that
	 *
	 * @param        $name
	 * @param string $default
	 * @param bool   $is_list_item
	 *
	 * @return array|bool|mixed|object|string
	 */
	public static function get_option( $name, $default = '', $is_list_item = false )
	{
		self::autoload();
		$name     = explode( '.', $name );
		$hasChild = ( count( $name ) > 1 ) ? TRUE : FALSE;

		if ( isset( $GLOBALS[ 'options' ][ $name[ 0 ] ] ) )
		{
			$option = $GLOBALS[ 'options' ][ $name[ 0 ] ];
		}else
		{
			$option = self::where( 'name', '=', $name[ 0 ] )->first();

			if ( ! $option )
			    return $default;

            $option = $option->value;

            if ( ! $option )
                return $option;
		}

		$option = is_object( json_decode( $option ) ) ? json_decode( $option, TRUE ) : $option;

		if ( $hasChild && is_array( $option ) )
		{
			$value = $option;
			for ( $i = 1; $i < count( $name ); $i++ )
			{
				$value = $value[ $name[ $i ] ];
			}

			$option = $value;
		}

		if ( $is_list_item )
		{
			$list_item   = [];
			$main_fields = [];
			foreach ( $option as $k => $o ) $main_fields[] = $k;

			foreach ( $option[ $main_fields[ 0 ] ] as $key => $item )
			{
				foreach ( $main_fields as $field )
				{
					$list_item[ $key ][ $field ] = $option[ $field ][ $key ];
				}
			}
			return $list_item;
		}

		return $option;
	}

	/**
	 * this function make an option in options table
	 *
	 * @param $name
	 * @param $value
	 * @param $is_autoload
	 *
	 * @return bool|Option
	 */
	public static function add_option( $name, $value = '', $is_autoload = false )
	{
		if ( mb_strlen( $name ) >= 200 || ! preg_match( '/^([\w,-]*)$/', $name ) )
		{
			return FALSE;
		}
		$option           = new self;
		$option->name     = $name;
		$option->value    = $value;
		$option->autoload = intval( $is_autoload );
		if ( $option->save() )
		{
			return $option;
		} else
		{
			return FALSE;
		}
	}

	/**
	 * this function update an option in options table
	 *
	 * @param $name
	 * @param $value
	 * @param $is_autoload
	 *
	 * @return bool|Option
	 */
	public static function update_option( $name, $value = '', $is_autoload = false )
	{
		if ( mb_strlen( $name ) >= 200 || ! preg_match( '/^([\w,-]*)$/', $name ) )
		{
			return FALSE;
		}
		$option = self::where( 'name', $name )->first();

		if ( $option )
		{
			$option->value    = $value;
			$option->autoload = intval( $is_autoload );
		} else
		{
			$option           = new self;
			$option->name     = $name;
			$option->value    = $value;
			$option->autoload = intval( $is_autoload );
		}
		if ( $option->save() )
		{
			return $option;
		} else
		{
			return FALSE;
		}
	}

	private static function autoload()
	{
		if ( ! isset( $GLOBALS[ 'options' ] ) )
		{
			$options       = self::where( 'autoload', '1' )
				->get();
			$options_array = [];

			if ( $options )
			{
				foreach ( $options as $option )
				{
					$options_array[ $option->name ] = $option->value;
				}
			}
			$GLOBALS[ 'options' ] = $options_array;
		}
	}
}
