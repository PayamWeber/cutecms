<?php

namespace App\Http\Controllers\Admin\User;

use App\Forms\UserForm;
use App\Helpers\AlertHelper;
use App\Http\Controllers\Controller;
use App\Models\UserMeta;
use App\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
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
        $this->form->validation( $request );
        $errors = [];
        $find   = User::where( 'name', $request->name )
            ->orWhere( 'email', $request->email )
            ->first();

        if ( isset( $find->name ) && $find->name == $request->name )
            $errors[] = lang( 'This user name already exist' );
        if ( isset( $find->email ) && $find->email == $request->email )
            $errors[] = lang( 'This email already exist' );

        if ( $errors )
        {
            foreach ( $errors as $error )
            {
                AlertHelper::make( $error, 'danger' );
            }
            return back()->withInput();
        } else
        {
            $model               = new User;
            $model->name         = $request->name;
            $model->nick_name    = $request->nick_name;
            $model->email        = $request->email;
            $model->password     = Hash::make( $request->password );
            $model->role_id      = $request->role_id;
            $model->capabilities = $request->capability;
            if ( $model->save() )
            {
                $model->set_meta( UserMeta::META_AVATAR, $request->avatar_id );
                AlertHelper::make( lang( 'Operation successful' ) );
                return redirect( route( 'admin.user.edit', [ 'user' => $model->id ] ) );
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
        $model                 = User::findOrFail( $model );
        $this->data[ 'form' ]  = $this->form->edit( $model );
        $this->data[ 'model' ] = $model;
        return view( 'admin.user.edit', $this->data );
    }

    /**
     * @param Request $request
     * @param User    $User
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update( Request $request, $model )
    {
        $model = User::findOrFail( $model );
        $this->form->validation( $request, true );
        $errors = [];
        $find   = User::where( 'name', $request->name )
            ->orWhere( 'email', $request->email )
            ->first();

        if ( ! User::is_deletable( $model->id ) )
            $errors[] = lang( 'This User is not editable' );
        if ( isset( $find->name ) && $find->name != $model->name && $find->name == $request->name )
            $errors[] = lang( 'This user name already exist' );
        if ( isset( $find->email ) && $find->email != $model->email && $find->email == $request->email )
            $errors[] = lang( 'This email already exist' );
        if ( $request->password && strlen( $request->password ) < 8 )
            $errors[] = lang( 'Password must be at least 8 characters' );

        if ( $errors )
        {
            foreach ( $errors as $error )
            {
                AlertHelper::make( $error, 'danger' );
            }
            return back();
        } else
        {
            $model->name         = $request->name;
            $model->nick_name    = $request->nick_name;
            $model->email        = $request->email;
            if ( $request->password )
                $model->password     = Hash::make( $request->password );
            $model->role_id      = $request->role_id;
            $model->capabilities = $request->capability;
            if ( $model->save() )
            {
                $model->set_meta( UserMeta::META_AVATAR, $request->avatar_id );
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile()
    {
        $model                 = auth()->user();
        $this->data[ 'form' ]  = $this->form->edit( $model, true );
        $this->data[ 'model' ] = $model;
        return view( 'admin.user.edit', $this->data );
    }

    /**
     * @param Request $request
     * @param User    $User
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function profile_update( Request $request, $model = null )
    {
        $model = auth()->user();
        $this->form->validation( $request, true, true );
        $errors = [];
        $find   = User::where( 'name', $request->name )
            ->orWhere( 'email', $request->email )
            ->first();

        if ( isset( $find->email ) && $find->email != $model->email && $find->email == $request->email )
            $errors[] = lang( 'This email already exist' );
        if ( $request->password && strlen( $request->password ) < 8 )
            $errors[] = lang( 'Password must be at least 8 characters' );

        if ( $errors )
        {
            foreach ( $errors as $error )
            {
                AlertHelper::make( $error, 'danger' );
            }
            return back();
        } else
        {
            $model->nick_name    = $request->nick_name ? $request->nick_name : '';
            $model->email        = $request->email;
            if ( $request->password )
                $model->password     = Hash::make( $request->password );
            if ( $model->save() )
            {
                $model->set_meta( UserMeta::META_AVATAR, $request->avatar_id );
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
    public function destroy( User $User )
    {
        if ( ! User::is_deletable( $User->id ) )
        {
            AlertHelper::make( lang( 'This User is not editable' ), 'danger' );
            return back();
        }
        $User->delete();
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
        $models = User::where( [
            [ 'id', '!=', '1' ],
        ] )->orderBy( 'created_at', 'desc' );

        if ( $request->get( 'search' ) )
            $models->where( 'name', 'LIKE', "%$request->search%" )
                ->orWhere( 'email', 'LIKE', "%$request->search%" );

        return $models->paginate( $this->data[ 'rows_per_page' ] );
    }
}
