<?php

namespace App\Models;

use App\Helpers\SafeMethods;

class MediaFolder extends BaseModel
{
	use SafeMethods;

	protected $table    = 'media_folder';
	protected $fillable = [
		'user_id',
		'parent_id',
		'name',
		'created_at',
		'updated_at',
	];

	public function media()
	{
		return $this->hasMany( Media::class, 'folder_id' );
	}

	public function user()
	{
		return $this->belongsTo( 'App\User', 'user_id' );
	}
}
