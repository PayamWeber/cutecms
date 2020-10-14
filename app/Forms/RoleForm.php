<?php

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Models\Capability;
use App\Models\CapabilityCat;

class RoleForm
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
            'title.required' => lang( 'Please enter title' ),
            'name.required' => lang( 'Please enter name' ),
        ] );
    }

    public function create( array $args = [] )
    {
        $form                               = new FormHelper();
        $this->args[ 'additional_options' ] = $this->additional_form_elements();

        $form->form_attributes = [
            'method' => 'post',
            'action' => route( 'admin.role.store' ),
        ];
        $form->title           = lang( 'Add' ) . ' ' . lang( 'Role' );
        $form->submit_text     = lang( 'Submit' );
        $form->fields          = array_merge( [
            'title' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'label' => lang( 'Title' ),
                    'placeholder' => lang( 'Title' ),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => old( 'title' ),
                ],
            ],
            'name' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'label' => lang( 'Name' ),
                    'placeholder' => lang( 'Only English' ),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => old( 'name' ),
                ],
            ],
            'is_admin' => [
                'type' => 'checkbox',
                'col' => 12,
                'properties' => [
                    'items' => [
                        [
                            'name' => 'is_admin',
                            'checked' => (bool) old( 'is_admin' ),
                            'value' => '1',
                            'label' => lang( 'Access to admin pages' ),
                            'id' => '',
                            'disabled' => false,
                        ],
                    ],
                    'cols' => 0,
                    'inline' => false,
                    'toggle' => true,
                ],
            ],
            'is_default' => [
                'type' => 'checkbox',
                'col' => 12,
                'properties' => [
                    'items' => [
                        [
                            'name' => 'is_default',
                            'checked' => (bool) old( 'is_default' ),
                            'value' => '1',
                            'label' => lang( 'Set as default Role' ),
                            'id' => '',
                            'disabled' => false,
                        ],
                    ],
                    'cols' => 0,
                    'inline' => false,
                    'toggle' => true,
                ],
            ],
            'capability' => [
                'type' => 'blank',
                'col' => 12,
                'properties' => [
                    'content' => "<h2>" . lang( 'Capabilities' ) . "</h2>",
                ],
            ],
        ], $this->args[ 'additional_options' ] );
        return $form->make();
    }

    public function edit( $model )
    {
        $form                               = new FormHelper();
        $this->args[ 'additional_options' ] = $this->additional_form_elements( $model );

        $form->form_attributes = [
            'method' => 'post',
            'action' => route( 'admin.role.update', [ 'role' => $model->id ] ),
        ];
        $form->title           = lang( 'Edit' ) . ' ' . lang( 'Role' );
        $form->submit_text     = lang( 'Update' );
        $form->fields          = array_merge( [
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
                    'label' => lang( 'Title' ),
                    'placeholder' => lang( 'Title' ),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => $model->title,
                ],
            ],
            'name' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang( 'Name' ),
                    'placeholder' => lang( 'Only English' ),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => $model->name,
                ],
            ],
            'is_admin' => [
                'type' => 'checkbox',
                'col' => 12,
                'properties' => [
                    'items' => [
                        [
                            'name' => 'is_admin',
                            'checked' => (bool) $model->is_admin,
                            'value' => '1',
                            'label' => lang( 'Access to admin pages' ),
                            'id' => '',
                            'disabled' => false,
                        ],
                    ],
                    'cols' => 0,
                    'inline' => false,
                    'toggle' => true,
                ],
            ],
            'is_default' => [
                'type' => 'checkbox',
                'col' => 12,
                'properties' => [
                    'items' => [
                        [
                            'name' => 'is_default',
                            'checked' => (bool) $model->is_default,
                            'value' => '1',
                            'label' => lang( 'Set as default Role' ),
                            'id' => '',
                            'disabled' => false,
                        ],
                    ],
                    'cols' => 0,
                    'inline' => false,
                    'toggle' => true,
                ],
            ],
            'capability' => [
                'type' => 'blank',
                'col' => 12,
                'properties' => [
                    'content' => "<h2>" . lang( 'Capabilities' ) . "</h2>",
                ],
            ],
        ], $this->args[ 'additional_options' ] );
        return $form->make();
    }

    protected function additional_form_elements( $model = null )
    {
        $cap_cats = CapabilityCat::orderBy( 'order' )->get();

        $additional_options = [];
        if ( $cap_cats )
        {
            foreach ( $cap_cats as $cat )
            {
                $additional_options[] = [
                    'type' => 'label',
                    'col' => 12,
                    'properties' => [
                        'label' => "<strong class='blue font-size-large'>" . lang( $cat->title ) . "</strong>",
                    ],
                ];
                $caps                 = Capability::filter_by_cat( $cat->id )->get();

                if ( $caps )
                {
                    $checkbox_caps = [];

                    foreach ( $caps as $cap )
                    {
                        $checked = false;
                        if ( old( 'capability' ) )
                            $checked = in_array( $cap->name, old( 'capability' ) );
                        if ( $model )
                        {
                            if ( is_string( $model->capabilities ) && $model->capabilities == 'all' )
                                $checked = true;
                            if ( is_array( $model->capabilities ) )
                                $checked = in_array( $cap->name, $model->capabilities );
                        }

                        $checkbox_caps[] = [
                            'name' => 'capability[]',
                            'checked' => $checked,
                            'value' => $cap->name,
                            'label' => lang( $cap->title ),
                            'id' => '',
                            'disabled' => false,
                        ];
                    }
                    $additional_options[] = [
                        'type' => 'checkbox',
                        'col' => 12,
                        'properties' => [
                            'items' => $checkbox_caps,
                            'cols' => 2,
                            'inline' => false,
                            'toggle' => false,
                        ],
                    ];
                }
            }
        }
        return $additional_options;
    }
}
