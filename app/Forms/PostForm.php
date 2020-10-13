<?php

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Models\Capability;
use App\Models\CapabilityCat;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Role;
use App\Models\UserMeta;

class PostForm
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
            'action' => route( 'admin.post.store' ),
            'wrapper' => false,
            'has_footer' => false,
        ];
        $form->title           = lang( 'Add' ) . ' ' . lang( 'Post' );
        $form->submit_text     = lang( 'Submit' );
        $form->fields          = [
            'title' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'label' => lang( 'Title' ),
                    'placeholder' => lang( 'Place the Post title here' ),
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
        $statuses = [];
        $types    = [];
        foreach ( Post::getStatuses() as $status_id => $status ) $statuses[ $status_id ] = $status[ 'name' ];
        foreach ( Post::getTypes() as $type_id => $type ) $types[ $type_id ] = $type[ 'name' ];

        $form->form_attributes = [
            'method' => 'get',
            'action' => route( 'admin.post.store' ),
            'in_widget' => false,
            'wrapper' => false,
            'token' => false,
        ];
        $form->title           = lang( 'Add' ) . ' ' . lang( 'Post' );
        $form->submit_text     = lang( $model ? 'Update' : 'Submit' );
        $form->fields          = [
            'status' => [
                'type' => 'select2',
                'col' => 12,
                'properties' => [
                    'data' => $statuses,
                    'label' => lang( 'Status' ),
                    'selected' => old( 'status', $model ? $model->status : 0 ),
                ],
            ],
            'type' => [
                'type' => 'select2',
                'col' => 12,
                'properties' => [
                    'data' => $types,
                    'label' => lang( 'Type' ),
                    'selected' => old( 'type', $model ? $model->type : 0 ),
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
            'action' => route( 'admin.post.update', [ 'post' => $model->id ] ),
            'has_footer' => false,
            'wrapper' => false,
        ];
        $form->title           = lang( 'Edit' ) . ' ' . lang( 'Post' );
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
                    'placeholder' => lang( 'Place the post title here' ),
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

    public function seoForm( $model = null )
    {
        $form = new FormHelper();

        $form->form_attributes = [
            'method' => 'post',
            'action' => route( 'admin.post.store' ),
            'wrapper' => false,
            'has_footer' => false,
            'token' => false
        ];
        $form->title           = lang( 'Seo' );
        $form->color           = 'warning';
        $form->id              = 'seo-settings';
        $form->fields          = [
            'seo_description' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'label' => lang( 'Description' ),
                    'placeholder' => lang( 'The post description that we put on meta tags' ),
                    'value' => old('seo_description', optional( optional($model)->seo )->description),
                ],
            ],
            'seo_keywords' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'label' => lang( 'Keywords' ),
                    'placeholder' => lang( 'The post keywords that we put on meta tags' ),
                    'value' => old('seo_keywords', optional( optional($model)->seo )->keywords),
                ],
            ],
        ];
        return $form->make();
    }

    public function categoriesForm( $model = null )
    {
        $form = new FormHelper();

        $form->form_attributes = [
            'method' => 'post',
            'action' => route( 'admin.post.store' ),
            'wrapper' => false,
            'has_footer' => false,
            'token' => false
        ];
        $form->title           = lang( 'Categories' );
        $form->color           = 'info';
        $form->id              = 'categories-form-card';
        $form->fields          = $this->categoryList( $model );
        return $form->make();
    }

    protected function categoryList( $model = null )
    {
        $categories         = PostCategory::where( 'parent_id', 0 )->get();
        $checkbox_cats      = [];
        $current_categories = old( 'categories', [] );

        if ( $model instanceof Post ) {
            $current_categories = $model->categories->pluck( 'id' )->toArray();
        }

        $additional_options = [];
        if ( $categories ) {
            foreach ( $categories as $cat ) {
                $checkbox_cats[] = [
                    'name' => 'categories[]',
                    'checked' => in_array( $cat->id, $current_categories ),
                    'value' => $cat->id,
                    'label' => $cat->title,
                    'id' => '',
                    'disabled' => false,
                ];
                if ( count( $cat->children ) ) {
                    $this->childCategoriesLoad( $cat->children, $checkbox_cats, $current_categories );
                }
            }
        }
        $additional_options[] = [
            'type' => 'checkbox',
            'col' => 12,
            'properties' => [
                'items' => $checkbox_cats,
                'cols' => 1,
                'inline' => false,
                'toggle' => false,
            ],
        ];
        return $additional_options;
    }

    public function childCategoriesLoad( $children, array &$items, array $current_categories, $sign_text = '--' )
    {
        foreach ( $children as $cat ) {
            $items[] = [
                'name' => 'categories[]',
                'checked' => in_array( $cat->id, $current_categories ),
                'value' => $cat->id,
                'label' => $sign_text . ' ' . $cat->title,
                'id' => '',
                'disabled' => false,
            ];
            if ( count( $cat->children ) ) {
                $this->childCategoriesLoad( $cat->children, $items, $current_categories, $sign_text . '--' );
            }
        }
    }
}
