<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class adminController extends Controller
{
    /**
     * redirect to dashboard
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectToDashboard()
    {
        return redirect( route('dashboard') );
    }

    public function logout( )
    {
        \Auth::logout();
        return redirect('/login');
    }

    public function set_lang( $lang )
    {
        auth()->user()->set_meta( Language::KEY, $lang );
        return back();
    }
}
