<?php

namespace App\Http\Controllers\Admin\Page;

use App\Forms\PageForm;
use App\Helpers\AlertHelper;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Hash;
use Illuminate\Http\Request;

class PageController extends Controller
{
	protected $form;
	protected $validation;
	public    $data;

	public function __construct( Request $request )
	{
		$this->form = new PageForm();
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index( Request $request )
	{
		$this->data[ 'rows_per_page' ] = 25;
		$this->data[ 'models' ]        = $this->filter( $request );
		$this->data[ 'counter' ]       = $this->data[ 'models' ]->total() - ( $this->data[ 'rows_per_page' ] * ( $this->data[ 'models' ]->currentPage() - 1 ) );
		return view( 'admin.page.index', $this->data );
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{
		$this->data[ 'form' ]          = $this->form->create();
		$this->data[ 'settings_form' ] = $this->form->settings();
		return view( 'admin.page.create', $this->data );
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
//		$find   = Page::where( 'name', $request->name )
//			->orWhere( 'email', $request->email )
//			->first();

		if ( $errors )
		{
			foreach ( $errors as $error )
			{
				AlertHelper::make( $error, 'danger' );
			}
			return back()->withInput();
		} else
		{
			$slug = Page::safeName( $request->get( 'slug' ) ? : $request->get( 'title' ) );
			$this->find_and_change_slug( $slug );
			$status = isset( Page::get_statuses()[ $request->get( 'status' ) ] ) ? $request->get( 'status' ) : Page::STATUS_PUBLISH;

			$model          = new Page;
			$model->user_id = auth()->user()->id;
			$model->title   = $request->get( 'title' );
			$model->slug    = $slug;
			$model->content = $request->get( 'content' );
			$model->status  = $status;

			if ( $model->save() )
			{
				AlertHelper::make( lang( 'Operation successful' ) );
				return redirect( route( 'admin.page.edit', [ 'page' => $model->id ] ) );
			} else
			{
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
		$model                         = Page::findOrFail( $model );
		$this->data[ 'form' ]          = $this->form->edit( $model );
		$this->data[ 'settings_form' ] = $this->form->settings( $model );
		$this->data[ 'model' ]         = $model;
		return view( 'admin.page.edit', $this->data );
	}

	/**
	 * @param Request $request
	 * @param User    $User
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update( Request $request, $model )
	{
		$model = Page::findOrFail( $model );
		$this->form->validation( $request, true );
		$errors = [];

		if ( $errors )
		{
			foreach ( $errors as $error )
			{
				AlertHelper::make( $error, 'danger' );
			}
			return back();
		} else
		{
			$slug = Page::safeName( $request->get( 'slug' ) ? : $request->get( 'title' ) );
			$this->find_and_change_slug( $slug, $model->id );
			$status = isset( Page::get_statuses()[ $request->get( 'status' ) ] ) ? $request->get( 'status' ) : Page::STATUS_PUBLISH;

			$model->user_id = auth()->user()->id;
			$model->title   = $request->get( 'title' );
			$model->slug    = $slug;
			$model->content = $request->get( 'content' );
			$model->status  = $status;

			if ( $model->save() )
			{
				AlertHelper::make( lang( 'Operation successful' ) );
				return back();
			} else
			{
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
		$model = Page::findOrFail( $model );
		$model->delete();
		AlertHelper::make( 'Operation successful' );
		return back();
	}

	public function show( Request $request, $slug )
	{
		$model = Page::where( 'slug', $slug )->first();

		if ( ! $model )
			return abort( 404 );

		$this->data[ 'model' ] = $model;
		global $theme;

		if ( file_exists( $theme[ 'path' ] . '/' . 'page-' . $model->id . '.blade.php' ) )
			return view( 'theme::page-' . $model->id, $this->data );

		if ( file_exists( $theme[ 'path' ] . '/' . 'page-' . $slug . '.blade.php' ) )
			return view( 'theme::page-' . $slug, $this->data );

		if ( file_exists( $theme[ 'path' ] . '/' . 'page.blade.php' ) )
			return view( 'theme::page', $this->data );

		return view( 'theme::index', $this->data );
	}

	/**
	 * @param Request $request
	 *
	 * @return mixed
	 */
	protected function filter( Request $request )
	{
		$models = Page::where( [
			[ 'id', '!=', '1' ],
		] )->orderBy( 'created_at', 'desc' );

		if ( $request->get( 'search' ) )
			$models->where( 'name', 'LIKE', "%$request->search%" )
				->orWhere( 'email', 'LIKE', "%$request->search%" );

		return $models->paginate( $this->data[ 'rows_per_page' ] );
	}

	private function find_and_change_slug( &$slug, $current_page_id = 0 )
	{
		if ( Page::where( 'slug', $slug )->where( 'id', '!=', $current_page_id )->first() )
		{
			$slug .= '-1';
			if ( Page::where( 'slug', $slug )->where( 'id', '!=', $current_page_id )->first() )
			{
				$this->find_and_change_slug( $slug );
			}
		}
	}
}
