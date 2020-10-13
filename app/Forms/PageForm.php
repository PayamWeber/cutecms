<?php

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Models\Capability;
use App\Models\CapabilityCat;
use App\Models\Page;
use App\Models\Role;
use App\Models\UserMeta;

class PageForm
{
	protected $args;

	public function __construct( array $args = [] )
	{
		$this->args = $args;
	}

	public function validation( $request, $edit = false, $profile = false )
	{
		$request->validate( [
			'title' => 'required',
		], [
			'title.required' => lang( 'Please enter Title' ),
		] );
	}

	public function create( array $args = [] )
	{
		$form = new FormHelper();

		$form->form_attributes = [
			'method' => 'post',
			'action' => route( 'admin.page.store' ),
			'wrapper' => false,
			'has_footer' => false,
		];
		$form->title           = lang( 'Add' ) . ' ' . lang( 'Page' );
		$form->submit_text     = lang( 'Submit' );
		$form->fields          = [
			'title' => [
				'type' => 'input',
				'col' => 12,
				'properties' => [
					'label' => lang( 'Title' ),
					'placeholder' => lang( 'Place the page title here' ),
					'value' => old( 'title' ),
				],
			],
			'slug' => [
				'type' => 'input',
				'col' => 12,
				'properties' => [
					'placeholder' => lang( 'Slug' ),
					'value' => old( 'slug' ),
				],
			],
			'upload_image' => [
				'type' => 'html',
				'col' => 12,
				'properties' => [
					'html' => make_media_uploader( [ 'type' => 'tinymce' ] ),
				],
			],
			'content' => [
				'type' => 'textarea',
				'col' => 12,
				'properties' => [
					'value' => old( 'content' ),
					'class' => 'tinymce-textarea',
				],
			],
		];
		return $form->make();
	}

	public function settings( $model = '' )
	{
		$form     = new FormHelper();
		$statuses = [
			'0' => lang( 'Choose a status' ),
		];
		foreach ( Page::get_statuses() as $status_id => $status ) $statuses[ $status_id ] = $status[ 'name' ];

		$roles_array = Role::all()->toArray();
		$roles       = [];
		if ( $roles_array )
		{
			foreach ( $roles_array as $role )
				$roles[ $role[ 'id' ] ] = $role[ 'title' ];
		}

		$form->form_attributes = [
			'method' => 'get',
			'action' => route( 'admin.page.store' ),
			'in_widget' => false,
			'wrapper' => false,
			'token' => false,
		];
		$form->title           = lang( 'Add' ) . ' ' . lang( 'Page' );
		$form->submit_text     = lang( $model ? 'Update' : 'Submit' );
		$form->fields          = [
			'status' => [
				'type' => 'select2',
				'col' => 12,
				'properties' => [
					'data' => $statuses,
					'label' => lang( 'Status' ),
					'selected' => $model ? $model->status : 0,
				],
			],
		];
		return $form->make();
	}

	public function edit( $model )
	{
		$form = new FormHelper();

		$form->form_attributes = [
			'method' => 'post',
			'action' => route( 'admin.page.update', [ 'page' => $model->id ] ),
			'has_footer' => false,
			'wrapper' => false,
		];
		$form->title           = lang( 'Edit' ) . ' ' . lang( 'Page' );
		$form->submit_text     = lang( 'Update' );
		$form->fields          = [
			'_method' => [
				'type' => 'hidden',
				'properties' => [
					'value' => 'patch',
				],
			],
			'title' => [
				'type' => 'input',
				'col' => 12,
				'properties' => [
					'label' => lang( 'Title' ),
					'placeholder' => lang( 'Place the page title here' ),
					'value' => $model->title,
				],
			],
			'slug' => [
				'type' => 'input',
				'col' => 12,
				'properties' => [
					'placeholder' => lang( 'Slug' ),
					'value' => $model->slug,
				],
			],
			'upload_image' => [
				'type' => 'html',
				'col' => 12,
				'properties' => [
					'html' => make_media_uploader( [ 'type' => 'tinymce' ] ),
				],
			],
			'content' => [
				'type' => 'textarea',
				'col' => 12,
				'properties' => [
					'value' => $model->content,
					'class' => 'tinymce-textarea',
				],
			],
		];
		return $form->make();
	}
}
