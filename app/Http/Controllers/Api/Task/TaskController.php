<?php

namespace App\Http\Controllers\Api\Task;

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
    public    $data;

    public function __construct( Request $request )
    {
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index( Request $request )
    {
        return _api_response( true, [
            'models' => $this->filter( $request )
        ] );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store( Request $request )
    {
        $this->validation( $request );
        $errors = [];

        if ( ! isset( Task::getPriorities()[$request->get( 'priority' )] ) ){
            $errors[] = lang( 'Priority is not valid' );
        }

        if ( $errors ) {
            return _api_response( false, $errors );
        } else {
            $model              = new Task();
            $model->user_id     = Auth::id();
            $model->status      = Task::STATUS_TODO;
            $model->priority    = $request->get( 'priority' );
            $model->title       = $request->get( 'title' );
            $model->description = $request->get( 'description' );

            if ( $model->save() ) {
                return _api_response( true, [
                    'id' => $model->id
                ] );
            } else {
                return _api_response( false, [
                    lang( 'there\'s a problem with saving data, Please try again' )
                ] );
            }
        }
    }

    /**
     * @param null    $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show( $id = null, Request $request )
    {
        $model                  = Task::filterByUser( is_user_can( 'task_edit_other' ) ? 'all' : auth()->user() )
            ->findOrFail( $id );
        return _api_response( true, [
            'model' => $model
        ] );
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
        $this->validation( $request, true );
        $errors = [];

        if ( ! isset( Task::getPriorities()[$request->get( 'priority' )] ) ){
            $errors[] = lang( 'Priority is not valid' );
        }

        if ( $errors ) {
            return _api_response( false, $errors );
        } else {
            $model->priority    = $request->get( 'priority' );
            $model->title       = $request->get( 'title' );
            $model->description = $request->get( 'description' );

            if ( $model->save() ) {
                return _api_response( true, [
                    'id' => $model->id
                ] );
            } else {
                return _api_response( false, [
                    lang( 'there\'s a problem with saving data, Please try again' )
                ] );
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
        return _api_response( true );
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

    public function validation( $request )
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
}
