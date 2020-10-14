<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $casts = [
        'capabilities' => 'json'
    ];

    /**
     * is role editable
     *
     * @param null $id
     *
     * @return bool
     */
    public static function is_editable( $id = null )
    {
        if ( ! $id )
            $id = auth()->user() ? auth()->user()->id : false;
        if ( ! $id )
            return false;

        $model = self::find( $id );

        if ( $model && $model->name == 'administrator' )
            return false;

        return true;
    }

	public static function get_default_role()
	{
		$model = self::where('is_default', '1')->first();
		return $model ? $model : false;
    }

    public static function findByName( $name )
    {
        return self::query()->where( 'name', $name )->first();
    }
}
