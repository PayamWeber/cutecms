<?php

namespace App\Models;

use App\Helpers\SafeMethods;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends BaseModel
{
    use SafeMethods, SoftDeletes;

	protected $table = 'pages';

	const STATUS_DRAFT   = 5;
	const STATUS_PUBLISH = 10;

	public function user()
	{
		return $this->belongsTo( 'App\User', 'user_id' );
	}

	/**
	 * @param string $except
	 *
	 * @return array
	 */
	public static function get_statuses( $except = '' )
	{
		$statuses = [
			self::STATUS_DRAFT => [
				'name' => lang( 'Draft' ),
				'color' => '#fff'
			],
			self::STATUS_PUBLISH => [
				'name' => lang( 'Publish' ),
				'color' => ''
			],
		];

		if ( $except && is_array( $except ) )
		{
			foreach ( $except as $name )
			{
				if ( isset( $statuses[ $name ] ) )
				{
					unset( $statuses[ $name ] );
				}
			}
		} else if ( $except && isset( $statuses[ $except ] ) )
		{
			unset( $statuses[ $except ] );
		}

		return $statuses;
	}

}
