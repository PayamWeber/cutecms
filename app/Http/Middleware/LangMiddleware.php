<?php

namespace App\Http\Middleware;

use App\Helpers\LangHelper;
use App\Models\Language;
use Closure;
use Zend\I18n\Translator\Translator;

class LangMiddleware
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
        $LangHelper = new LangHelper();
        $LangHelper->set_languages();
        $LangHelper->make_translator();

        return $next($request);
    }
}
