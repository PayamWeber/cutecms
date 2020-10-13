<?php

namespace App\Models;

use App\Helpers\SafeMethods;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends BaseModel
{
    use SafeMethods;

    protected $table = 'post_categories';

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_categories_relation', 'category_id', 'post_id');
    }

    public function children()
    {
        return $this->hasMany( PostCategory::class, 'parent_id' );
    }
}
