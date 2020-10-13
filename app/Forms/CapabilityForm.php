<?php

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Models\CapabilityCat;

class CapabilityForm
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
            'parent' => 'required|numeric|min:1'
        ], [
            'title.required' => lang('Please enter title'),
            'name.required' => lang('Please enter name'),
            'parent.min' => lang('Please enter Category'),
        ] );
    }

    public function create( array $args = [] )
    {
        $caps    = CapabilityCat::orderBy( 'title', 'ASC' )->get();
        $parents = [];

        $parents[ '0' ] = lang('Choose ...');
        foreach ( $caps as $cap ) $parents[ $cap->id ] = $cap->title;

        $form_helper = new FormHelper();

        $form_helper->form_attributes = [
            'method' => 'post',
            'action' => route( 'programming.capabilities.index' ),
        ];
        $form_helper->title           = lang('Add') . ' ' . lang('Capability');
        $form_helper->submit_text     = lang('Submit');
        $form_helper->footer_buttons  = [
            [
                'title' => 'Test',
                'color' => 'danger',
                'attr' => [
                    'type' => 'button',
                ],
            ],
        ];
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
            'route' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'label' => lang('Route Name'),
                    'placeholder' => '...',
                    'icon' => 'fa-bolt',
                    'icon_append' => true,
                    'attr' => [
                        'style="direction:ltr;text-align:left;"',
                    ],
                    'value' => old( 'route' ),
                ],
            ],
            'parent' => [
                'type' => 'select2',
                'col' => 12,
                'properties' => [
                    'data' => $parents,
                    'label' => lang('Category'),
                    'selected' => 0,
                ],
            ],
        ];
        return $form_helper->make();
    }

    public function edit( $capability )
    {
        $caps    = CapabilityCat::orderBy( 'title', 'ASC' )->get();
        $parents = [];

        $parents[ '0' ] = lang('Choose ...');
        foreach ( $caps as $cap ) $parents[ $cap->id ] = $cap->title;

        $form_helper = new FormHelper();

        $form_helper->form_attributes = [
            'method' => 'post',
            'action' => route( 'programming.capabilities.update', [ $capability->id ] ),
        ];
        $form_helper->title           = lang('Edit') . ' ' . lang('Capability');
        $form_helper->submit_text     = lang('Update');
        $form_helper->footer_buttons  = [
            [
                'title' => 'Test',
                'color' => 'danger',
                'attr' => [
                    'type' => 'button',
                ],
            ],
        ];
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
                    'value' => $capability->title,
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
                    'value' => $capability->name,
                ],
            ],
            'route' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'label' => lang('Route Name'),
                    'placeholder' => '...',
                    'icon' => 'fa-bolt',
                    'icon_append' => true,
                    'attr' => [
                        'style="direction:ltr;text-align:left;"',
                    ],
                    'value' => ( strpos( $capability->route, '[' ) !== false ) ? implode( ',', json_decode( $capability->route ) ) : $capability->route,
                ],
            ],
            'parent' => [
                'type' => 'select2',
                'col' => 12,
                'properties' => [
                    'data' => $parents,
                    'label' => lang('Category'),
                    'selected' => $capability->parent,
                ],
            ],
        ];
        return $form_helper->make();
    }
}
