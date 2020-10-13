<?php

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Models\PostCategory;

class PostCategoryForm
{
    protected $args;

    public function __construct( array $args = [] )
    {
        $this->args = $args;
    }

    public function validation( $request, $edit = false )
    {
        $request->validate( [
            'title' => 'required',
            'parent' => 'required|numeric|min:0'
        ], [
            'title.required' => lang('Please enter title'),
            'parent.min' => lang('Please enter Category'),
        ] );
    }

    public function create( array $args = [] )
    {
        $cats    = PostCategory::orderBy( 'title', 'ASC' )->get();
        $parents = [];

        $parents[ '0' ] = lang('No Parent');
        foreach ( $cats as $cat ) $parents[ $cat->id ] = $cat->title;

        $form_helper = new FormHelper();

        $form_helper->form_attributes = [
            'method' => 'post',
            'action' => route( 'admin.post_category.index' ),
        ];
        $form_helper->title           = lang('Add') . ' ' . lang('Category');
        $form_helper->submit_text     = lang('Submit');
        $form_helper->fields  = [
            'title' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang('Title'),
                    'placeholder' => lang('Title'),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => old( 'title' ),
                ],
            ],
            'slug' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang('Slug'),
                    'placeholder' => lang('Only English'),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => old( 'slug' ),
                ],
            ],
            'parent' => [
                'type' => 'select2',
                'col' => 12,
                'properties' => [
                    'data' => $parents,
                    'label' => lang('Parent'),
                    'selected' => old('parent', 0),
                ],
            ],
        ];
        return $form_helper->make();
    }

    public function edit( $catability )
    {
        $cats    = PostCategory::orderBy( 'title', 'ASC' )->get();
        $parents = [];

        $parents[ '0' ] = lang('No Parent');
        foreach ( $cats as $cat ) $parents[ $cat->id ] = $cat->title;

        $form_helper = new FormHelper();

        $form_helper->form_attributes = [
            'method' => 'post',
            'action' => route( 'admin.post_category.update', [ $catability->id ] ),
        ];
        $form_helper->title           = lang('Edit') . ' ' . lang('Category');
        $form_helper->submit_text     = lang('Update');
        $form_helper->fields  = [
            '_method' => [
                'type' => 'hidden',
                'properties' => [
                    'value' => 'patch',
                ],
            ],
            'title' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang('Title'),
                    'placeholder' => lang('Title'),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => $catability->title,
                ],
            ],
            'slug' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang('Slug'),
                    'placeholder' => lang('Only English'),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => $catability->slug,
                ],
            ],
            'parent' => [
                'type' => 'select2',
                'col' => 12,
                'properties' => [
                    'data' => $parents,
                    'label' => lang('Parent'),
                    'selected' => $catability->parent_id,
                ],
            ],
        ];
        return $form_helper->make();
    }
}
