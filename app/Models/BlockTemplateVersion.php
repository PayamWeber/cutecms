<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockTemplateVersion extends Model
{
    protected $table = 'block_template_version';

    public function user()
    {
        return $this->belongsTo( 'App\User', 'user_id' );
    }

    public function cats()
    {
        return $this->belongsToMany( 'App\Models\BlockCategory', 'block_category_relation', 'template_id', 'category_id' );
    }

}
