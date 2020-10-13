<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
	const META_AVATAR = '_avatar_id';

	protected $table    = 'user_meta';
	protected $fillable = [ 'user_id', 'name', 'value' ];

	public function user()
	{
		return $this->belongsTo( 'App\Models\User', 'user_id' );
	}

	public static function get( $name, $default = '', $user = null )
	{
		if ( ! $user )
			$user = auth()->user()->id;
		$meta = self::where( [ [ 'name', $name ], [ 'user_id', $user ] ] )->first();
		return $meta ? ( $meta->value ? $meta->value : $default ) : $default;
	}

	public static function set( $name, $value = '', $user = null )
	{
		if ( ! $user )
			$user = auth()->user() ? auth()->user()->id : false;

		if ( ! $user )
			return false;
		if ( ! $name )
			return false;

		$meta = self::where( [
			'name' => $name,
			'user_id' => $user,
		] )->first();

		$value = $value ?? '';
		if ( $meta )
		{
			$meta->value = $value;
			return $meta->save();
		} else
		{
			$meta          = new self;
			$meta->user_id = $user;
			$meta->name    = $name;
			$meta->value   = $value;
			return $meta->save();
		}
	}
}
