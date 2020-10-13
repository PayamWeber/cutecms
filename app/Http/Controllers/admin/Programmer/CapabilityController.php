<?php

namespace App\Http\Controllers\Admin\Programmer;

use App\Forms\CapabilityForm;
use App\Helpers\AlertHelper;
use App\Http\Controllers\Controller;
use App\Models\Capability;
use App\Models\CapabilityCat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class CapabilityController extends Controller
{
    protected $form;
    protected $validation;
    public    $data;

    public function __construct( Request $request )
    {
        $this->form                    = new CapabilityForm();
        $this->data[ 'rows_per_page' ] = 25;
        $this->data[ 'models' ]        = $this->filter( $request );
        $this->data[ 'counter' ]       = $this->data[ 'models' ]->total() - ( $this->data['rows_per_page'] * ( $this->data[ 'models' ]->currentPage() - 1 ) );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index( Request $request )
    {
        $this->data[ 'form' ] = $this->form->create();
        return view( 'admin/programmer/capabilities/index', $this->data );
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
        $find   = Capability::where( 'name', $request->name )->count();

        if ( $find )
            $errors[] = lang( 'This name already exist' );

        if ( $errors )
        {
            foreach ( $errors as $error )
            {
                AlertHelper::make( $error, 'danger' );
            }
            return back()->withInput();
        } else
        {
            $model         = new Capability;
            $model->title  = $request->title;
            $model->name   = $request->name;
            $model->parent = $request->parent;
            $model->route  = ( strpos( $request->route, ',' ) !== false ) ? json_encode( explode( ',', $request->route ) ) : ( $request->route ? $request->route : '' );
            if ( $model->save() )
            {
                AlertHelper::make( lang( 'Operation successful' ) );
                return back();
            } else
            {
                AlertHelper::make( lang( 'there\'s a problem with saving data, Please try again' ), 'danger' );
                return back()->withInput();
            }
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit( Capability $capability )
    {
        $this->data[ 'form' ] = $this->form->edit($capability);
        return view( 'admin/programmer/capabilities/edit', $this->data );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update( Request $request, $model )
    {
        $model = Capability::findOrFail( $model );
        $this->form->validation( $request );
        $errors = [];
        $find   = Capability::where( 'name', $request->name )->first();

        if ( isset( $find->name ) && $find->name != $model->name )
            $errors[] = lang( 'This name already exist' );

        if ( $errors )
        {
            foreach ( $errors as $error )
            {
                AlertHelper::make( $error, 'danger' );
            }
            return back();
        } else
        {
            $model->title  = $request->title;
            $model->name   = $request->name;
            $model->parent = $request->parent;
            $model->route  = ( strpos( $request->route, ',' ) !== false ) ? json_encode( explode( ',', $request->route ) ) : ( $request->route ? $request->route : '' );
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

    public function destroy( Capability $capability )
    {
        $capability->delete();
        AlertHelper::make( lang('Operation successful') );
        return back();
    }

    protected function filter( Request $request )
    {
        $models = Capability::where( [] )->orderBy( 'id', 'desc' );

        if ( $request->get( 'search' ) )
            $models->where( 'name', 'LIKE', "%$request->search%" )
                ->orWhere( 'title', 'LIKE', "%$request->search%" )
                ->orWhere( 'route', 'LIKE', "%$request->search%" );

        return $models->paginate( $this->data[ 'rows_per_page' ] );
    }

}
