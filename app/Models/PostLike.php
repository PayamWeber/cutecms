<?php

namespace App\Models;

use App\Helpers\SafeMethods;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostLike extends BaseModel
{
    protected $table = 'post_likes';

    public $timestamps = false;

    protected $casts = [
        'date' => 'timestamp'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
