<?php

namespace App\Http\Controllers\Admin\User;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class PageController extends Controller
{
	protected $form;
	protected $validation;
	public    $data;

	public function __construct( Request $request )
	{
		$this->form = new UserForm();
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index( Request $request )
	{
		$this->data[ 'rows_per_page' ] = 25;
		$this->data[ 'models' ]        = $this->filter( $request );
		$this->data[ 'counter' ]       = $this->data[ 'models' ]->total() - ( $this->data[ 'rows_per_page' ] * ( $this->data[ 'models' ]->currentPage() - 1 ) );
		return view( 'admin.user.index', $this->data );
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{
		$this->data[ 'form' ] = $this->form->create();
		return view( 'admin.user.create', $this->data );
	}

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store( Request $request )
	{
		$request->validate( [
			'title' => 'required',
			'name' => 'required',
			'structure' => 'required',
		], [
			'title.required' => lang( 'Please enter the page title' ),
			'name.required' => lang( 'Please enter the page name' ),
			'structure.required' => lang( 'Please enter the page structure' ),
		] );

		$errors    = [];
		$safe_name = Page::safe_name( $request->get( 'name' ) );
		$find      = Page::where( 'name', $safe_name )
			->first();
		$result    = [
			'type' => 'error',
			'messages' => [],
		];

		if ( $find )
			$errors[] = lang( 'This name already exist' );

		if ( $errors )
		{
			$result[ 'messages' ] = $errors;
			return json_encode( $result, JSON_UNESCAPED_UNICODE );
		} else
		{
			$model          = new Page;
			$model->user_id = auth()->user()->id;
			$model->name    = $safe_name;
			$model->title   = $request->get( 'title' );
			$model->status  = Page::STATUS_DRAFT;
			$model->hash    = md5( $safe_name );
			if ( $model->save() )
			{
				$result[ 'type' ]       = 'success';
				$result[ 'messages' ][] = lang( "Page saved successfully" );
				return json_encode( $result, JSON_UNESCAPED_UNICODE );
			} else
			{
				$result[ 'messages' ] = $errors;
				return json_encode( $result, JSON_UNESCAPED_UNICODE );
			}
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function publish( Request $request, $model )
	{
		$model = User::find( $model );
		$result    = [
			'type' => 'error',
			'messages' => [],
		];

		if ( $model )
		{
			$request->validate( [
				'title' => 'required',
				'name' => 'required',
				'structure' => 'required',
			], [
				'title.required' => lang( 'Please enter the page title' ),
				'name.required' => lang( 'Please enter the page name' ),
				'structure.required' => lang( 'Please enter the page structure' ),
			] );

			$errors    = [];
			$safe_name = Page::safe_name( $request->get( 'name' ) );
			$find      = Page::where( 'name', $safe_name )
				->first();
			$result    = [
				'type' => 'error',
				'messages' => [],
			];

			if ( $find )
				$errors[] = lang( 'This name already exist' );

			if ( $errors )
			{
				$result[ 'messages' ] = $errors;
				return json_encode( $result, JSON_UNESCAPED_UNICODE );
			} else
			{
				$model         = new Page;
				$model->name   = $safe_name;
				$model->title  = $request->get( 'title' );
				$model->status = Page::STATUS_DRAFT;
				$model->hash   = md5( $safe_name );
				if ( $model->save() )
				{
					$result[ 'type' ]       = 'success';
					$result[ 'messages' ][] = lang( "Page saved successfully" );
					return json_encode( $result, JSON_UNESCAPED_UNICODE );
				} else
				{
					$result[ 'messages' ] = $errors;
					return json_encode( $result, JSON_UNESCAPED_UNICODE );
				}
			}
		}else
		{
			$result[ 'messages' ][] = lang('Page not found');
			return json_encode( $result, JSON_UNESCAPED_UNICODE );
		}


	}

	public function validate_repeated_page_name( $name, $current_page_id = 0 )
	{
		$find = Page::where( 'name', $name )
			->where( 'id', '!=', $current_page_id )
			->select( 'id' )
			->first();

		if ( $find )
			return false;
		else
			return true;
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	protected function filter( Request $request )
	{
		$models = Page::orderBy( 'created_at', 'desc' );

		if ( $request->get( 'search' ) )
			$models->where( 'name', 'LIKE', "%$request->search%" )
				->orWhere( 'email', 'LIKE', "%$request->search%" );

		return $models->paginate( $this->data[ 'rows_per_page' ] );
	}
}
