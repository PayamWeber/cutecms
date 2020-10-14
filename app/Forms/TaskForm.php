<?php

namespace App\Forms;

use App\Helpers\FormHelper;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Task;

class TaskForm
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
            'description' => 'required',
            'priority' => 'required|numeric|min:0',
        ], [
            'title.required' => lang( 'Please enter title' ),
            'description.required' => lang( 'Please enter description' ),
            'priority.required' => lang( 'Please enter Priority' ),
        ] );
    }

    public function create( array $args = [] )
    {
        $priorities = [];
        foreach ( Task::getPriorities() as $p_id => $priority ) $priorities[ $p_id ] = $priority[ 'name' ];
        $form_helper = new FormHelper();

        $form_helper->form_attributes = [
            'method' => 'post',
            'action' => route( 'admin.task.store' ),
        ];
        $form_helper->title           = lang( 'Add' ) . ' ' . lang( 'Task' );
        $form_helper->submit_text     = lang( 'Submit' );
        $form_helper->fields          = [
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
            'description' => [
                'type' => 'textarea',
                'col' => 12,
                'properties' => [
                    'label' => lang( 'Description' ),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => old( 'description' ),
                ],
            ],
            'priority' => [
                'type' => 'select2',
                'col' => 12,
                'properties' => [
                    'data' => $priorities,
                    'label' => lang( 'Priority' ),
                    'selected' => old( 'priority', Task::PRIORITY_MEDIUM ),
                ],
            ],
        ];
        return $form_helper->make();
    }

    public function edit( $task )
    {
        $priorities = [];
        foreach ( Task::getPriorities() as $p_id => $priority ) $priorities[ $p_id ] = $priority[ 'name' ];
        $form_helper = new FormHelper();

        $form_helper->form_attributes = [
            'method' => 'post',
            'action' => route( 'admin.task.update', [ $task->id ] ),
        ];
        $form_helper->title           = lang( 'Edit' ) . ' ' . lang( 'Task' );
        $form_helper->submit_text     = lang( 'Update' );
        $form_helper->fields          = [
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
                    'placeholder' => lang( 'Title' ),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => old( 'title', $task->title ),
                ],
            ],
            'description' => [
                'type' => 'textarea',
                'col' => 12,
                'properties' => [
                    'label' => lang( 'Description' ),
                    'icon' => 'fa-bolt',
                    'icon_append' => false,
                    'value' => old( 'description', $task->description ),
                ],
            ],
            'priority' => [
                'type' => 'select2',
                'col' => 12,
                'properties' => [
                    'data' => $priorities,
                    'label' => lang( 'Priority' ),
                    'selected' => old( 'priority', $task->priority ),
                ],
            ],
        ];
        return $form_helper->make();
    }
}
