<?php

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Models\UserMeta;

class MediaForm
{
    protected $args;

    public function __construct( array $args = [] )
    {
        $this->args = $args;
    }

    public function validation( $request, $edit = false, $profile = false )
    {
        $request->validate( [
        ], [
        ] );
    }

    public function edit( $model )
    {
        $form = new FormHelper();

        $action                = route( 'admin.media.update', [ 'id' => $model->id ] );
        $form->form_attributes = [
            'method' => 'post',
            'action' => $action,
        ];
        $form->title           = lang( 'Edit' ) . ' ' . lang( 'Media' );
        $form->submit_text     = lang( 'Update' );
        $form->footer_buttons  = [
            [
                'title' => Lang( 'Delete' ),
                'color' => 'danger',
                'attr' => [
                    'href' => route( 'admin.media.destroy', [ 'id' => $model->id ] ),
                ],
            ],
        ];
        $form->fields          = [
            '_method' => [
                'type' => 'hidden',
                'properties' => [
                    'value' => 'patch',
                ],
            ],
            'url' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'label' => lang( 'File Url' ),
                    'placeholder' => lang( 'File Url' ),
                    'value' => get_media_url( $model ),
                    'attr' => [ 'dir="ltr"', 'readonly' ],
                ],
            ],
            'name' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'label' => lang( 'File Title' ),
                    'placeholder' => lang( 'File Title' ),
                    'value' => $model->name,
                ],
            ],
        ];
        return $form->make();
    }
}
