<?php

namespace App\Helpers;

trait SafeMethods
{
	/**
	 * @param $name
	 *
	 * @return string
	 */
	public static function safeName( $name )
	{
		if ( is_string( $name ) && $name )
		{
//			preg_match_all( '/([A-Za-z0-9\-\_]+)/', $name, $output_array );
			$name = str_replace( ' ', '-', trim( $name ) );
			preg_match_all( '/([A-Za-z0-9\-\_]*[^\x00-\xFF]*\s*)/u', $name, $output_array );
			$safe = implode( '', $output_array[ 0 ] );

			if ( mb_strlen( implode( '', $output_array[ 0 ] ) ) > 100 )
				$safe = mb_substr( $safe, 0, 90 );

			return $safe;
		} else
		{
			return '';
		}
	}

	/**
	 * @param $name
	 *
	 * @return mixed|string
	 */
	public static function safeTitle( $name )
	{
		if ( ! empty( strval( $name ) ) )
		{
			$name = strval( str_replace( ' ', 'sspacess', $name ) );
			preg_match_all( '/([A-Za-z0-9\-\_\(\)]*[^\x00-\xFF]*\s*)/u', $name, $output_array );
			$safe = implode( '', $output_array[ 0 ] );
			$safe = str_replace( 'sspacess', ' ', $safe );

			return $safe;
		} else
		{
			return '';
		}
	}
}