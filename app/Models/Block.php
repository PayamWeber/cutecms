<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $table = 'block';

    public function user()
    {
        return $this->belongsTo( 'App\User', 'user_id' );
    }

    public function page()
    {
        return $this->belongsTo( 'App\Models\Page', 'page_id' );
    }

}
