<?php

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Models\CapabilityCat;

class CapabilityCatForm
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
            'name' => 'required',
        ], [
            'title.required' => lang('Please enter title'),
            'name.required' => lang('Please enter name'),
        ] );
    }

    public function create( array $args = [] )
    {
        $form = new FormHelper();

        $form->form_attributes = [
            'method' => 'post',
            'action' => route( 'programming.capability_cats.index' ),
        ];
        $form->title           = lang('Add') . ' ' . lang('Capability Category');
        $form->submit_text     = lang('Submit');
        $form->fields  = [
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
            'name' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang('Name'),
                    'placeholder' => lang('Only English'),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => old( 'name' ),
                ],
            ],
            'order' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'label' => lang('Sort Order'),
                    'type' => 'number',
                    'value' => old( 'order' ),
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
            'action' => route( 'programming.capability_cats.update', [ $model->id ] ),
        ];
        $form->title           = lang('Edit') . ' ' . lang('Capability Category');
        $form->submit_text     = lang('Update');
        $form->fields  = [
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
                    'value' => $model->title,
                ],
            ],
            'name' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang('Name'),
                    'placeholder' => lang('Only English'),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => $model->name,
                ],
            ],
            'order' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'label' => lang('Sort Order'),
                    'type' => 'number',
                    'value' => $model->order,
                ],
            ],
        ];
        return $form->make();
    }
}
