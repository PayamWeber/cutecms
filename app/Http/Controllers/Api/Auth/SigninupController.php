<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SigninupController extends Controller
{

    public function login( Request $request )
    {
        $request->validate( [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ] );
        $user = User::where( 'email', $request->email )->first();

        if ( $user )
        {
            if ( Hash::check( $request->password, $user->password ) )
            {
                $token_result = $user->createToken( 'PAT' );

                return _api_response( true, [
                    'access_token' => $token_result->plainTextToken,
                    'token_type' => 'Bearer',
                    'name' => $user->nick_name
                ] );
            } else
            {
                return _api_response( false, [ 'ایمیل یا رمز عبور وارد شده غلط میباشد' ] );
            }
        } else
        {
            return _api_response( false, [ 'ایمیل یا رمز عبور وارد شده غلط میباشد' ] );
        }
    }
}
