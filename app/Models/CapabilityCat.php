<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapabilityCat extends Model
{
    protected $table      = 'capability_cats';
    protected $fillable   = [
        'title',
        'name',
    ];
    public    $timestamps = false;

    public function caps()
    {
        return $this->hasMany( 'App\Models\Capability', 'parent' );
    }
}
