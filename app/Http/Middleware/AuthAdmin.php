<?php

namespace App\Http\Middleware;

use Closure;
use Zend\I18n\Translator\Translator;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( ! is_admin() )
            return abort(404);

        return $next($request);
    }
}
