<?php

namespace App\Models;

use App\Helpers\BaseModelFeatures;
use App\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class BaseModel extends Model
{
	use BaseModelFeatures;

	private static $_instance = null;

    /**
     * @param null $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
	public static function filterByUser( $user = null )
	{
	    $query = self::query();
		// filter by all users
		if ( $user === 'all' )
			return $query->where( 'user_id', '!=', 0 );

		$user = $user instanceof User ? $user->id : $user;
		$user = $user ? : ( auth()->user() ? auth()->user()->id : 0 );

		if ( ! $user )
			return $query->where( 'user_id', 0 );

		return $query->where( 'user_id', $user );
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

    /**
     * @param null $user
     *
     * @return mixed
     */
    public function scopeFilterByUser( $user = null )
    {
        // filter by all users
        if ( $user === 'all' )
            return $this->where( 'user_id', '!=', 0 );

        $user = $user instanceof User ? $user->id : $user;
        $user = $user ? : ( auth()->user() ? auth()->user()->id : 0 );

        if ( ! $user )
            return $this->where( 'user_id', 0 );

        return $this->where( 'user_id', $user );
    }
}
