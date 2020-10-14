<?php

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Models\Capability;
use App\Models\CapabilityCat;
use App\Models\Role;
use App\Models\UserMeta;

class UserForm
{
    protected $args;

    public function __construct( array $args = [] )
    {
        $this->args = $args;
    }

    public function validation( $request, $edit = false, $profile = false )
    {
        $request->validate( [
            'name' => $profile ? '' : 'required',
            'email' => 'required|email',
            'password' => $edit ? '' : 'required|min:8',
            'role_id' => $profile ? '' : 'required',
        ], [
            'name.required' => lang( 'Please enter User Name' ),
            'email.required' => lang( 'Please enter Email' ),
            'email.email' => lang( 'Please enter a Valid Email' ),
            'password.required' => lang( 'Please enter Password' ),
            'password.min' => lang( 'Password must be at least 8 characters' ),
            'role_id.required' => lang( 'Please enter Role' ),
        ] );
    }

    public function create( array $args = [] )
    {
        $form = new FormHelper();
        $this->args['additional_options'] = $this->additional_form_elements();

        $roles_array = Role::all()->toArray();
        $roles = [];
        if ( $roles_array )
        {
            foreach ( $roles_array as $role )
                $roles[$role['id']] = $role['title'];
        }

        $form->form_attributes = [
            'method' => 'post',
            'action' => route( 'admin.user.store' ),
        ];
        $form->title           = lang( 'Add' ) . ' ' . lang( 'User' );
        $form->submit_text     = lang( 'Submit' );
        $form->fields          = array_merge( [
            'avatar_id' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'type' => 'hidden',
                    'value' => old( 'avatar_id' ),
                ],
            ],
            'name' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang( 'User Name' ),
                    'placeholder' => lang( 'User Name' ),
                    'value' => old( 'name' ),
                ],
            ],
            'nick_name' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang( 'Nick Name' ),
                    'placeholder' => lang( 'Nick Name' ),
                    'value' => old( 'nick_name' ),
                ],
            ],
            'email' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang( 'Email' ),
                    'placeholder' => lang( 'Email' ),
                    'type' => 'email',
                    'value' => old( 'email' ),
                ],
            ],
            'password' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang( 'Password' ),
                    'placeholder' => lang( 'Password' ),
                    'type' => 'password',
                    'value' => '',
                ],
            ],
            'role_id' => [
                'type' => 'select2',
                'col' => 12,
                'properties' => [
                    'data' => $roles,
                    'label' => lang('Role'),
                    'selected' => 0,
                ],
            ],
            'capability' => [
                'type' => 'blank',
                'col' => 12,
                'properties' => [
                    'content' => "<h2>" . lang( 'Capabilities' ) . "</h2>",
                ],
            ],
        ], $this->args['additional_options'] );
        return $form->make();
    }

    public function edit( $model, $is_profile = false )
    {
        $form = new FormHelper();
        $this->args['additional_options'] = $this->additional_form_elements( $model, $is_profile );

        $roles_array = Role::all()->toArray();
        $roles = [];
        if ( $roles_array )
        {
            foreach ( $roles_array as $role )
                $roles[$role['id']] = lang( $role['title'] );
        }

        $action = $is_profile ? route('admin.profile.update') : route( 'admin.user.update', [ 'user' => $model->id ] );
        $form->form_attributes = [
            'method' => 'post',
            'action' => $action,
        ];
        $form->title           = lang( 'Edit' ) . ' ' . lang( 'User' );
        $form->submit_text     = lang( 'Update' );
        $form->fields          = array_merge( [
            '_method' => [
                'type' => 'hidden',
                'properties' => [
                    'value' => 'patch',
                ],
            ],
            'avatar_id' => [
                'type' => 'input',
                'col' => 12,
                'properties' => [
                    'type' => 'hidden',
                    'value' => $model->meta( UserMeta::META_AVATAR ),
                ],
            ],
            'name' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang( 'User Name' ),
                    'placeholder' => lang( 'User Name' ),
                    'value' => $model->name,
                    'disabled' => $is_profile
                ],
            ],
            'nick_name' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang( 'Nick Name' ),
                    'placeholder' => lang( 'Nick Name' ),
                    'value' => $model->nick_name,
                ],
            ],
            'email' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang( 'Email' ),
                    'placeholder' => lang( 'Email' ),
                    'type' => 'email',
                    'value' => $model->email,
                ],
            ],
            'password' => [
                'type' => 'input',
                'col' => 6,
                'properties' => [
                    'label' => lang( 'Password' ),
                    'placeholder' => lang( 'Password' ),
                    'type' => 'password',
                    'value' => '',
                ],
            ],
            'role_id' => [
                'type' => $is_profile ? 'hidden' : 'select2',
                'col' => 12,
                'properties' => [
                    'data' => $roles,
                    'label' => lang('Role'),
                    'selected' => $model->role_id,
                ],
            ],
        ], $this->args['additional_options'] );
        return $form->make();
    }

    protected function additional_form_elements( $model = null, $is_profile = false )
    {
        if ( $is_profile )
            return [];

        $cap_cats = CapabilityCat::orderBy('order')->get();

        $additional_options = [];
        if ( $cap_cats )
        {
            $additional_options['capability'] = [
                'type' => 'blank',
                'col' => 12,
                'properties' => [
                    'content' => "<h2>" . lang( 'Capabilities' ) . "</h2>",
                ],
            ];
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
                        if ( old('capability') )
                            $checked = in_array( $cap->name, old('capability') );
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
