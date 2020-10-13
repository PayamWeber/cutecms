<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable                $exception
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ( $request->wantsJson() ){
            if ( $exception instanceof ValidationException ){
                $code = Response::HTTP_UNPROCESSABLE_ENTITY;
            }else if ( $exception instanceof AuthenticationException ){
                $code = 401;
            }else{
                $code = 0;
            }

            if ( $code == 0 ){
                if ( ! config('app.debug') ){
                    return _api_response(
                        false,
                        [ 'مشکل فنی در سمت سرور پیش آمد لطفا بعد از چند دقیقه مجددا تلاش نمایید' ],
                        $exception->getCode() ? : 500
                    );
                }
            }else{
                $errors = [ $exception->getMessage() ];

                if ( method_exists( $exception, 'errors' ) ){
                    $errors = $exception->errors();
                }

                $errors = is_array( $errors ) ? Arr::flatten( $errors ) : [];

                return _api_response( false, $errors, $code );
            }
        }

        return parent::render($request, $exception);
    }
}
