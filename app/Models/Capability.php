<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capability extends Model
{
    protected $table      = 'capabilities';
    public    $timestamps = false;

    public function cat()
    {
        return $this->belongsTo( 'App\Models\CapabilityCat', 'parent' );
    }

    public function get_category( $cap_id )
    {
        $capability = self::where( 'id', $cap_id )->first();
        $cat        = CapabilityCat::where( 'id', $capability->parent )->first();

        return $cat;
    }

    public static function filter_by_cat( $cat )
    {
        return self::where('parent', $cat);
    }

    public static function get_route_by_name( $capability_name )
    {
        if ( ! $capability_name )
            return false;

        $capability = self::where( 'name', $capability_name )->first();

        if ( ! $capability )
        {
            return false;
        }

        return ( is_array( json_decode( $capability->route, true ) ) ) ? json_decode( $capability->route, true ) : $capability->route;
    }
}
