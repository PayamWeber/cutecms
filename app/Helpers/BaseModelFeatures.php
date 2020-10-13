<?php

namespace App\Helpers;

trait BaseModelFeatures
{

	/**
	 * @return string
	 */
	public static function getTableName( $add_prefix = true )
	{
		if ( ! self::$_instance instanceof static )
			self::$_instance = new static();

		$prefix = \DB::getTablePrefix();
		return ( $add_prefix ? $prefix : '' ) . self::$_instance->table;
	}

	/**
	 * @param $column_name
	 *
	 * @return bool
	 */
	public static function columnExists( $column_name )
	{
		$first = static::select( '*' )->first();

		return array_key_exists( $column_name, $first->getAttributes() );
	}

	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public function hasAttribute( $key )
	{
		return array_key_exists( $key, $this->getAttributes() );
	}

	/**
	 * @param        $values
	 * @param string $index
	 *
	 * @return bool
	 */
	public static function multipleIndexUpdate( $values, $index = 'id' )
	{
		if ( $values && is_array( $values ) )
		{
			$indexes   = [];
			$cases_sql = [];

			$i = 1;
			foreach ( $values[ 0 ] as $column_name => $c )
			{
				if ( $column_name == $index )
					continue;

				$cases_sql[] = "`$column_name` = CASE ";
				foreach ( $values as $value )
				{
					$index_value = $value[ $index ] ?? '';

					if ( $index_value )
					{
						$indexes[ $index_value ] = $index_value;

						foreach ( $value as $_column_name => $column_value )
						{
							if ( $_column_name == $column_name && $_column_name != $index )
							{
								$index_value_sql  = addslashes( $index_value );
								$column_value_sql = addslashes( $column_value );

								if ( ! is_numeric( $index_value_sql ) )
									$index_value_sql = "'$index_value_sql'";
								if ( ! is_numeric( $column_value_sql ) )
									$column_value_sql = "'$column_value_sql'";

								$cases_sql[] = " WHEN `{$index}` = {$index_value_sql} THEN {$column_value_sql} ";
							}
						}
					}
				}
				$cases_sql[] = " END";
				if ( $i !== count( $values[ 0 ] ) - 1 )
					$cases_sql[] = ", ";
				$i++;
			}

			$final_cases_sql   = implode( '', $cases_sql );
			$final_indexes_sql = implode( ', ', $indexes );
			$table_name        = static::getTableName();

//			return "UPDATE `{$table_name}` SET {$final_cases_sql} WHERE `{$index}` IN ({$final_indexes_sql})';";
			$success = \DB::update( "UPDATE `{$table_name}` SET {$final_cases_sql} WHERE `{$index}` IN ({$final_indexes_sql});" );

			return $success;
		} else
		{
			return false;
		}
	}
}