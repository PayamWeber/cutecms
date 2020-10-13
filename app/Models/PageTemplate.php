<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTemplate extends Model
{
    protected $table = 'page_template';

    public function user()
    {
        return $this->belongsTo( 'App\User', 'user_id' );
    }

    public function cats()
    {
        return $this->belongsToMany( 'App\Models\PageCategory', 'page_category_relation', 'template_id', 'category_id' );
    }

}
