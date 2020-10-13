<?php

namespace App\Http\Controllers\Admin\User;

use App\Forms\RoleForm;
use App\Helpers\AlertHelper;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
{
    protected $form;
    protected $validation;
    public    $data;

    public function __construct( Request $request )
    {
        $this->form                    = new RoleForm();
        $this->data[ 'rows_per_page' ] = 25;
        $this->data[ 'models' ]        = $this->filter( $request );
        $this->data[ 'counter' ]       = $this->data[ 'models' ]->total() - ( $this->data[ 'rows_per_page' ] * ( $this->data[ 'models' ]->currentPage() - 1 ) );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->data[ 'form' ] = $this->form->create();
        return view( 'admin.user.role.index', $this->data );
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
        $find   = Role::where( 'name', $request->name )->count();

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
            $model               = new Role;
            $model->title        = $request->title;
            $model->name         = $request->name;
            $model->is_admin     = $request->is_admin ? '1' : '0';
            $model->is_default   = $request->is_default ? '1' : '0';
            $model->capabilities = json_encode( $request->capability, JSON_UNESCAPED_UNICODE );

            // switch default role
            if ( $request->is_default )
            {
                $find = Role::where( [
                    [ 'is_default', '=', '1' ],
                ] )->first();
            }

            if ( $model->save() )
            {
                if ( $request->is_default && $find )
                {
                    $find->is_default = '0';
                    $find->save();
                }

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
     * @param Role $Role
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit( Role $Role )
    {
        $this->data[ 'form' ] = $this->form->edit( $Role );
        return view( 'admin.user.role.edit', $this->data );
    }

    /**
     * @param Request $request
     * @param Role    $Role
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update( Request $request, $model )
    {
        $model = Role::findOrFail( $model );
        $this->form->validation( $request );
        $errors = [];
        $find   = Role::where( 'name', $request->name )->first();

        if ( ! Role::is_editable( $model->id ) )
            $errors[] = lang( 'This Role is not editable' );
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
            $model->title        = $request->title;
            $model->name         = $request->name;
            $model->is_admin     = $request->is_admin ? '1' : '0';
            $model->is_default   = $request->is_default ? '1' : '0';
            $model->capabilities = json_encode( $request->capability, JSON_UNESCAPED_UNICODE );

            // switch default role
            if ( $request->is_default )
            {
                $find = Role::where( [
                    [ 'is_default', '=', '1' ],
                    [ 'id', '!=', $model->id ],
                ] )->first();
            }

            if ( $model->save() )
            {
                if ( $request->is_default && $find )
                {
                    $find->is_default = '0';
                    $find->save();
                }
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
     * @param Role $Role
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy( Role $Role )
    {
        if ( ! Role::is_editable( $Role->id ) )
        {
            AlertHelper::make( lang( 'This Role is not editable' ), 'danger' );
            return back();
        }
        $Role->delete();
        AlertHelper::make( 'Operation successful' );
        return back();
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    protected function filter( Request $request )
    {
        $models = Role::where( [] )->orderBy( 'id', 'desc' );

        if ( $request->get( 'search' ) )
            $models->where( 'name', 'LIKE', "%$request->search%" )
                ->orWhere( 'title', 'LIKE', "%$request->search%" );

        return $models->paginate( $this->data[ 'rows_per_page' ] );
    }
}
