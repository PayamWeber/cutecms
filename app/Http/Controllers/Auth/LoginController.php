<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\CryptHelper;
use App\Http\Controllers\Controller;
use App\Models\OauthClient;
use App\Rules\CaptchaValidation;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = '/admin';

	/**
	 * Show the application's login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showLoginForm()
	{
		return view( 'auth.login' );
	}

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware( 'guest' )->except( 'logout' );
	}

	/**
	 * Validate the user login request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return void
	 *
	 * @throws \Illuminate\Validation\ValidationException
	 */
	protected function validateLogin( Request $request )
	{
		$this->validate( $request, [
			$this->username() => 'required|string',
			'password' => 'required|string',
			'captcha' => [ 'required', new CaptchaValidation() ],
		] );
	}

	public function LoginApi( Request $request )
	{
		//(new CaptchaValidation)->passes( '', $request->get('captcha') )
		header( 'Content-Type: application/json' );
		$errors    = [];
		$decrypted = CryptHelper::decrypt_aes_256( $request->get( 'data' ), CryptHelper::LOGIN_SECRET_PASSCODE );
		$decrypted = $decrypted ? json_decode( $decrypted, true ) : '';


		if ( $decrypted && is_array( $decrypted ) )
		{
			$decrypted[ 'user_name' ] = $decrypted[ 'user_name' ] ?? '';
			$decrypted[ 'password' ] = $decrypted[ 'password' ] ?? '';
			$decrypted[ 'secret_code' ] = $decrypted[ 'secret_code' ] ?? '';

			if ( $decrypted[ 'user_name' ] && $decrypted[ 'password' ] && OauthClient::verify( $decrypted[ 'secret_code' ] ) )
			{
				$model = User::where( 'name', $decrypted[ 'user_name' ] )
					->orWhere( 'email', $decrypted[ 'user_name' ] )
					->first();

				if ( $model )
				{
					if ( ( $model->role && $model->role->is_admin == '1' ) || ! Hash::check( $decrypted[ 'password' ], $model->password ) )
					{
						$errors[] = 'wrong-password';
					} else if ( ! Hash::check( $decrypted[ 'password' ], $model->password ) )
					{
						$errors[] = 'wrong-password';
					}
				} else
				{
					$errors[] = 'user-not-found';
				}
			}else
			{
				$errors[] = 'fields-empty';
			}
		}else
		{
			$errors[] = 'fields-empty';
		}


		if ( $errors )
		{
			return json_encode( [
				'type' => 'error',
				'messages' => $errors,
			] );
		} else
		{
			return json_encode( [
				'type' => 'success',
				'nickname' => $model->nick_name,
				'avatar' => $model->avatar,
			], JSON_UNESCAPED_UNICODE );
		}
	}
}
