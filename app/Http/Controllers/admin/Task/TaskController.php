<?php

namespace App\Http\Controllers\Admin\Task;

use App\Forms\PostCategoryForm;
use App\Forms\TaskForm;
use App\Helpers\AlertHelper;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Task;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $form;
    protected $validation;
    public    $data;

    public function __construct( Request $request )
    {
        $this->form = new TaskForm();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index( Request $request )
    {
        $this->data[ 'form' ]          = $this->form->create();
        $this->data[ 'rows_per_page' ] = 25;
        $this->data[ 'models' ]        = $this->filter( $request );
        return view( 'admin.task.index', $this->data );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store( Request $request )
    {
        $this->form->validation( $request );
        $errors = [];

        if ( $errors ) {
            foreach ( $errors as $error ) {
                AlertHelper::make( $error, 'danger' );
            }
            return back()->withInput();
        } else {
            $model              = new Task();
            $model->user_id     = Auth::id();
            $model->status      = Task::STATUS_TODO;
            $model->priority    = $request->get( 'priority' );
            $model->title       = $request->get( 'title' );
            $model->description = $request->get( 'description' );

            if ( $model->save() ) {
                AlertHelper::make( lang( 'Operation successful' ) );
                return back();
            } else {
                AlertHelper::make( lang( 'there\'s a problem with saving data, Please try again' ), 'danger' );
                return back()->withInput();
            }
        }
    }

    /**
     * @param null    $model
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit( $id = null, Request $request )
    {
        $model                  = Task::filterByUser( is_user_can( 'task_edit_other' ) ? 'all' : auth()->user() )
            ->findOrFail( $id );
        $this->data[ 'form' ]   = $this->form->edit( $model );
        $this->data[ 'models' ] = $this->filter( $request );
        $this->data[ 'model' ]  = $model;
        return view( 'admin.task.edit', $this->data );
    }

    /**
     * @param Request $request
     * @param         $model
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update( Request $request, $model )
    {
        $model = Task::filterByUser( is_user_can( 'task_edit_other' ) ? 'all' : auth()->user() )
            ->findOrFail( $model );
        $this->form->validation( $request, true );
        $errors = [];

        if ( $errors ) {
            foreach ( $errors as $error ) {
                AlertHelper::make( $error, 'danger' );
            }
            return back();
        } else {
            $model->priority    = $request->get( 'priority' );
            $model->title       = $request->get( 'title' );
            $model->description = $request->get( 'description' );

            if ( $model->save() ) {
                AlertHelper::make( lang( 'Operation successful' ) );
                return back();
            } else {
                AlertHelper::make( lang( 'there\'s a problem with saving data, Please try again' ), 'danger' );
                return back();
            }
        }
    }

    /**
     * @param Request $request
     * @param         $model
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function move( Request $request, $model )
    {
        $model = Task::filterByUser( is_user_can( 'task_edit_other' ) ? 'all' : auth()->user() )
            ->findOrFail( $model );
        $this->validate( $request, [
            'status' => 'required'
        ], [
            'status.required' => lang( 'Please enter status' ),
        ] );
        $errors = [];

        if ( $errors ) {
            foreach ( $errors as $error ) {
                AlertHelper::make( $error, 'danger' );
            }
            return back();
        } else {
            $model->status    = $request->get( 'status' );

            if ( $model->save() ) {
                AlertHelper::make( lang( 'Operation successful' ) );
                return back();
            } else {
                AlertHelper::make( lang( 'there\'s a problem with saving data, Please try again' ), 'danger' );
                return back();
            }
        }
    }

    /**
     * @param User $User
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy( Request $request, $model )
    {
        $model = Task::filterByUser( is_user_can( 'task_delete_other' ) ? 'all' : auth()->user() )
            ->findOrFail( $model );
        $model->delete();
        AlertHelper::make( lang( 'Operation successful' ) );
        return back();
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    protected function filter( Request $request )
    {
        $models = Task::query()->latest();

        $models->filterByUser( is_user_can( 'task_index_other' ) ? 'all' : auth()->user() );

        if ( $request->get( 'search' ) )
            $models->where( 'title', 'LIKE', "%$request->search%" )
                ->orWhere( 'description', 'LIKE', "%$request->search%" );

        return $models->get();
    }
}
