<?php

namespace App;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\UserMeta;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @method static UserFactory factory()
 * @package App
 */
class User extends Authenticatable
{
	use Notifiable, HasApiTokens, HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password', 'nick_name',
	];

	protected $casts = [
	    'capabilities' => 'json'
    ];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	/**
	 * @param        $name
	 * @param string $value
	 *
	 * @return mixed
	 */
	public function set_meta( $name, $value = '' )
	{
		return UserMeta::set( $name, $value, $this->id );
	}

    /**
     * @param        $name
     * @param string $default
     *
     * @return string
     */
	public function meta( $name, $default = '' )
	{
		return UserMeta::get( $name, $default, $this->id );
	}

    /**
     * @param        $name
     * @param string $default
     *
     * @return string
     */
	public function get_meta( $name, $default = '' )
	{
		return $this->meta( $name, $default );
	}

    /**
     * @return bool|\Illuminate\Contracts\Routing\UrlGenerator|string
     */
	public function getAvatarAttribute()
	{
		$avatar = $this->meta( UserMeta::META_AVATAR );
		if ( $avatar )
		{
			return get_media_url( $avatar, 'thumbnail' );
		}
		return false;
	}

    /**
     * @return bool|string
     */
	public function getAvatarIdAttribute()
	{
		$avatar = $this->meta( UserMeta::META_AVATAR );
		if ( $avatar )
		{
			return $avatar;
		}
		return false;
	}

    /**
     * @return array|string
     */
	public function getCapsAttribute()
	{
		$self_capabilities = $this->capabilities;
		$self_capabilities = is_array( $self_capabilities ) ? $self_capabilities : [];
		$capabilities      = $this->role->capabilities;
		$capabilities      = is_array( $capabilities ) ? $capabilities : [];

		if ( in_array('all', $self_capabilities) || in_array('all', $capabilities) )
			$caps = ['all'];
		else
			$caps = array_merge( $capabilities, $self_capabilities );

		return $caps;
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
	public function role()
	{
		return $this->belongsTo( 'App\Models\Role', 'role_id' );
	}

    /**
     * @param $id
     *
     * @return bool
     */
	public static function is_deletable( $id )
	{
		if ( $id == 1 )
			return false;
		return true;
	}

	/**
	 * is user admin
	 *
	 * @param null $id
	 *
	 * @return bool
	 */
	public function isAdmin()
	{
		if ( $this->capabilities == 'all' )
			return true;

		$role = $this->role;

		if ( $role && $role->is_admin == 1 )
			return true;

		return false;
	}

	/**
	 * is user can
	 *
	 * @param        $capability
	 * @param string $operator
	 * @param null   $user_id
	 *
	 * @return bool
	 */
	public static function isUserCan( $capability, $operator = 'OR', $user_id = null )
	{
		$user = is_object( $user_id ) ? $user_id : '';
		$user = $user_id && ! $user ? self::find( $user_id ) : auth()->user();

		if ( ! $user )
			return false;

		if ( in_array('all', $user->caps) )
			return true;

		if ( is_array( $capability ) )
		{
			if ( strtolower( $operator ) == 'and' )
				if ( count( array_intersect( $capability, $user->caps ) ) == count( $capability ) )
					return true;
			if ( strtolower( $operator ) == 'or' )
				if ( count( array_intersect( $capability, $user->caps ) ) >= 1 )
					return true;
		} else if ( is_string( $capability ) )
		{
			if ( in_array( $capability, $user->caps ) )
				return true;
		}

		/*
		if ( ! $user->role )
			return false;

		if ( $user->role->capabilities == 'all' )
			return true;

		$capabilities = json_decode( $user->role->capabilities, true );

		if ( is_array( $capability ) )
		{
			if ( strtolower( $operator ) == 'and' )
				if ( count( array_intersect( $capability, $capabilities ) ) == count( $capability ) )
					return true;
			if ( strtolower( $operator ) == 'or' )
				if ( count( array_intersect( $capability, $capabilities ) ) >= 1 )
					return true;
		} else if ( is_string( $capability ) )
		{
			if ( in_array( $capability, $capabilities ) )
				return true;
		}
		*/

		return false;
	}

    /**
     * @param        $capability
     * @param string $operator
     *
     * @return bool
     */
    public function hasPerm( $capability, $operator = 'OR' )
    {
        return self::isUserCan( $capability, $operator, $this );
	}
}
