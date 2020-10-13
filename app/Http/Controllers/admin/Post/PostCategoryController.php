<?php

namespace App\Http\Controllers\Admin\Post;

use App\Forms\PostCategoryForm;
use App\Helpers\AlertHelper;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostCategoryController extends Controller
{
    protected $form;
    protected $validation;
    public    $data;

    public function __construct( Request $request )
    {
        $this->form = new PostCategoryForm();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index( Request $request )
    {
        $this->data[ 'form' ]          = $this->form->create();
        $this->data[ 'rows_per_page' ] = 25;
        $this->data[ 'models' ]        = $this->filter( $request );
        $this->data[ 'counter' ]       = $this->data[ 'models' ]->total() - ( $this->data[ 'rows_per_page' ] * ( $this->data[ 'models' ]->currentPage() - 1 ) );
        return view( 'admin.post.categories.index', $this->data );
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
            $slug = PostCategory::safeName( $request->get( 'slug' ) ? : $request->get( 'title' ) );
            $this->find_and_change_slug( $slug );

            $model            = new PostCategory();
            $model->user_id   = Auth::id();
            $model->parent_id = $request->get( 'parent' );
            $model->title     = $request->get( 'title' );
            $model->slug      = $slug;

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
     * @param User $User
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit( $model = null, Request $request )
    {
        $model                         = PostCategory::findOrFail( $model );
        $this->data[ 'form' ]          = $this->form->edit( $model );
        $this->data[ 'rows_per_page' ] = 25;
        $this->data[ 'models' ]        = $this->filter( $request );
        $this->data[ 'counter' ]       = $this->data[ 'models' ]->total() - ( $this->data[ 'rows_per_page' ] * ( $this->data[ 'models' ]->currentPage() - 1 ) );
        $this->data[ 'model' ]         = $model;
        return view( 'admin.post.categories.edit', $this->data );
    }

    /**
     * @param Request $request
     * @param User    $User
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update( Request $request, $model )
    {
        $model = PostCategory::findOrFail( $model );
        $this->form->validation( $request, true );
        $errors = [];

        if ( $errors ) {
            foreach ( $errors as $error ) {
                AlertHelper::make( $error, 'danger' );
            }
            return back();
        } else {
            $slug = PostCategory::safeName( $request->get( 'slug' ) ? : $request->get( 'title' ) );
            $this->find_and_change_slug( $slug );

            $model->parent_id = $request->get( 'parent' );
            $model->title     = $request->get( 'title' );
            $model->slug     = $slug;

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
        $model = PostCategory::findOrFail( $model );
        $model->delete();
        AlertHelper::make( lang( 'Operation successful' ) );
        return back();
    }

    public function show( Request $request, $slug )
    {
        $model = PostCategory::where( 'slug', $slug )->first();

        if ( ! $model )
            return abort( 404 );

        $this->data[ 'model' ] = $model;
        global $theme;

        if ( file_exists( $theme[ 'path' ] . '/' . 'page-' . $model->id . '.blade.php' ) )
            return view( 'theme::page-' . $model->id, $this->data );

        if ( file_exists( $theme[ 'path' ] . '/' . 'page-' . $slug . '.blade.php' ) )
            return view( 'theme::page-' . $slug, $this->data );

        return view( 'theme::index', $this->data );
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    protected function filter( Request $request )
    {
        $models = PostCategory::orderBy( 'created_at', 'desc' );

        if ( $request->get( 'search' ) )
            $models->where( 'name', 'LIKE', "%$request->search%" )
                ->orWhere( 'slug', 'LIKE', "%$request->search%" );

        return $models->paginate( $this->data[ 'rows_per_page' ] );
    }

    private function find_and_change_slug( &$slug, $current_page_id = 0 )
    {
        if ( PostCategory::where( 'slug', $slug )->where( 'id', '!=', $current_page_id )->first() ) {
            $slug .= '-1';
            if ( PostCategory::where( 'slug', $slug )->where( 'id', '!=', $current_page_id )->first() ) {
                $this->find_and_change_slug( $slug );
            }
        }
    }
}
