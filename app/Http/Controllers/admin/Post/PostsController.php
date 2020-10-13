<?php

namespace App\Http\Controllers\Admin\Post;

use App\Forms\PostForm;
use App\Helpers\AlertHelper;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
{
    protected $form;
    protected $validation;
    public    $data;

    public function __construct( Request $request )
    {
        $this->form = new PostForm();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index( Request $request )
    {
        $this->data[ 'rows_per_page' ] = 25;
        $this->data[ 'models' ]        = $this->filter( $request );
        $this->data[ 'counter' ]       = $this->data[ 'models' ]->total() - ( $this->data[ 'rows_per_page' ] * ( $this->data[ 'models' ]->currentPage() - 1 ) );
        return view( 'admin.post.index', $this->data );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->data[ 'categories_form' ] = $this->form->categoriesForm();
        $this->data[ 'seo_form' ]   = $this->form->seoForm();
        $this->data[ 'settings_form' ]   = $this->form->settings();
        $this->data[ 'form' ]            = $this->form->create();
        return view( 'admin.post.create', $this->data );
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
            $slug = Post::safeName( $request->get( 'slug' ) ? : $request->get( 'title' ) );
            $this->find_and_change_slug( $slug );
            $status = isset( Post::getStatuses()[ $request->get( 'status' ) ] ) ? $request->get( 'status' ) : Post::STATUS_PUBLISH;
            $type   = isset( Post::getTypes()[ $request->get( 'type' ) ] ) ? $request->get( 'type' ) : Post::TYPE_DEFAULT;

            $model           = new Post;
            $model->user_id  = Auth::id();
            $model->image_id = $request->get( 'image' );
            $model->title    = $request->get( 'title' );
            $model->slug     = $slug;
            $model->content  = $request->get( 'content' ) ? : '';
            $model->status   = $status;
            $model->type     = $type;
            $model->views    = 0;
            $model->likes    = 0;

            if ( $model->save() ) {
                $model->categories()->sync($request->get('categories', []) ? : []);

                AlertHelper::make( lang( 'Operation successful' ) );
                return redirect( route( 'admin.post.edit', [ 'post' => $model->id ] ) );
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
    public function edit( $model = null )
    {
        $model                         = Post::findOrFail( $model );
        $this->data[ 'form' ]          = $this->form->edit( $model );
        $this->data[ 'settings_form' ] = $this->form->settings( $model );
        $this->data[ 'categories_form' ] = $this->form->categoriesForm( $model );
        $this->data[ 'model' ]         = $model;
        return view( 'admin.post.edit', $this->data );
    }

    /**
     * @param Request $request
     * @param User    $User
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update( Request $request, $model )
    {
        $model = Post::findOrFail( $model );
        $this->form->validation( $request, true );
        $errors = [];

        if ( $errors ) {
            foreach ( $errors as $error ) {
                AlertHelper::make( $error, 'danger' );
            }
            return back();
        } else {
            $slug = Post::safeName( $request->get( 'slug' ) ? : $request->get( 'title' ) );
            $this->find_and_change_slug( $slug );
            $status = isset( Post::getStatuses()[ $request->get( 'status' ) ] ) ? $request->get( 'status' ) : Post::STATUS_PUBLISH;
            $type   = isset( Post::getTypes()[ $request->get( 'type' ) ] ) ? $request->get( 'type' ) : Post::TYPE_DEFAULT;

            $model->image_id = $request->get( 'image' );
            $model->title    = $request->get( 'title' );
            $model->slug     = $slug;
            $model->content  = $request->get( 'content' ) ? : '';
            $model->status   = $status;
            $model->type     = $type;

            if ( $model->save() ) {
                $model->categories()->sync($request->get('categories', []) ? : []);

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
        $model = Post::findOrFail( $model );
        $model->delete();
        AlertHelper::make( 'Operation successful' );
        return back();
    }

    public function show( Request $request, $slug )
    {
        $model = Post::where( 'slug', $slug )->first();

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
        $models = Post::orderBy( 'created_at', 'desc' );

        if ( $request->get( 'search' ) )
            $models->where( 'name', 'LIKE', "%$request->search%" )
                ->orWhere( 'email', 'LIKE', "%$request->search%" );

        return $models->paginate( $this->data[ 'rows_per_page' ] );
    }

    private function find_and_change_slug( &$slug, $current_page_id = 0 )
    {
        if ( Post::where( 'slug', $slug )->where( 'id', '!=', $current_page_id )->first() ) {
            $slug .= '-1';
            if ( Post::where( 'slug', $slug )->where( 'id', '!=', $current_page_id )->first() ) {
                $this->find_and_change_slug( $slug );
            }
        }
    }
}
