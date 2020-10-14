<?php

namespace App\Models;

use App\Helpers\SafeMethods;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostSeo extends BaseModel
{
    protected $table = 'posts_seo';

    public $timestamps = false;

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
