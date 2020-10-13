<?php

namespace App\Models;

use App\Helpers\BaseModelFeatures;
use App\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	use BaseModelFeatures;

	private static $_instance = null;

	/**
	 * @param null $user
	 *
	 * @return mixed
	 */
	public static function filterByUser( $user = null )
	{
		// filter by all users
		if ( $user === 'all' )
			return self::where( 'user_id', '!=', 0 );

		$user = $user instanceof User ? $user->id : $user;
		$user = $user ? : ( auth()->user() ? auth()->user()->id : 0 );

		if ( ! $user )
			return self::where( 'user_id', 0 );

		return self::where( 'user_id', $user );
	}

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
