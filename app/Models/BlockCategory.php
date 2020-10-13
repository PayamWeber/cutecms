<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockCategory extends Model
{
    protected $table = 'block_category';

	public $timestamps = false;

    public function blocks()
    {
        return $this->belongsToMany( 'App\Models\BlockTemplate', 'block_category_relation', 'category_id', 'template_id' );
    }

}
