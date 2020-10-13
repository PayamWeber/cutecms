<?php

namespace App\Http\Controllers\Api\Post;

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
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index( Request $request )
    {
        $models = Post::query()->latest();

        if ( $request->get( 'search' ) )
            $models->where( 'name', 'LIKE', "%$request->search%" )
                ->orWhere( 'email', 'LIKE', "%$request->search%" );

        return _api_response( true, [
            'posts' => $models->paginate( 25 )->items()
        ] );
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
