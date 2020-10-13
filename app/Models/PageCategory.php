<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageCategory extends Model
{
    protected $table = 'page_category';

    public $timestamps = false;

    public function pages()
    {
        return $this->belongsToMany( 'App\Models\PageTemplate', 'page_category_relation', 'category_id', 'template_id' );
    }

}
