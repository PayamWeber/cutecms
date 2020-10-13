<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // changing public path
        $this->change_public_path();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function change_public_path()
    {
        $public_path = public_path();
        if ( env('PROTECTED_FOLDER_NAME') && strpos( $public_path, env('PROTECTED_FOLDER_NAME') ) )
        {
            $this->app->bind( 'path.public', function () {
                $base_path = base_path();
                return str_replace( env('PROTECTED_FOLDER_NAME'), env('PUBLIC_FOLDER_NAME'), $base_path );
            } );
        }
    }
}
