<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Models\Media;
use App\Models\Post;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get( '/', 'HomeController@index' );
Route::get( '/getinsta', function () {
    $instagram = new \InstagramScraper\Instagram();
    $medias    = $instagram->getMedias( 'zerostrix', 50 );

    if ( $medias ) {
        foreach ( $medias as $post ) {
            $find_same = Post::where( 'instagram_id', $post->getId() )->first();

            if ( ! $find_same ) {
                $image               = Media::uploadFileFromUrl( $post->getImageHighResolutionUrl(), $post->getId() . '.jpg' );
                $model               = new Post;
                $model->user_id      = User::first()->id;
                $model->image_id     = $image ? $image->id : 0;
                $model->instagram_id = $post->getId();
                $model->title      = mb_substr( str_replace( "\n", '', Post::safeTitle( $post->getCaption() ) ), 0, 100 );
                $model->slug       = mb_substr( str_replace( "\n", '', Post::safeName( $post->getCaption() ) ), 0, 100 );
                $model->content    = $post->getCaption();
                $model->status     = Post::STATUS_PUBLISH;
                $model->type       = Post::TYPE_INSTAGRAM;
                $model->views      = $post->getLikesCount() * 3;
                $model->likes      = $post->getLikesCount();
                $model->created_at = Carbon::createFromTimestamp( $post->getCreatedTime() );
                $model->updated_at = Carbon::createFromTimestamp( $post->getCreatedTime() );

                try{
                    $model->save();
                }catch ( Exception $exception ){

                }
            }
        }
    }
} );
//Auth::routes();
Route::get( 'login', 'Auth\LoginController@showLoginForm' )->name( 'login' );
Route::post( 'login', 'Auth\LoginController@login' );

/*
 * captcha
 */
Route::get( 'captcha', 'Auth\CaptchaController@show' );

/**
 * admin routes
 */
Route::middleware( [ 'auth', 'admin', 'capability' ] )
    ->prefix( 'admin' )
    ->group( function () {
        //        logout
        Route::get( '/logout', 'admin\adminController@logout' );

        //        languages
        Route::get( '/set_language/{lang}', 'admin\adminController@set_lang' );

        //        dashboard
        Route::get( '/', 'admin\adminController@redirectToDashboard' );
        Route::get( '/dashboard', 'admin\dashboardController@index' )->name( 'dashboard' );

        Route::group( [ 'as' => 'programming.' ], function () {
            //        capabilities
            Route::resource( '/programmer/capabilities', 'Admin\Programmer\CapabilityController', [ 'except' => [ 'show', 'create' ] ] );

            //        capability cats
            Route::resource( '/programmer/capability_cats', 'Admin\Programmer\CapabilityCatController', [ 'except' => [ 'show', 'create' ] ] );
        } );

        Route::group( [ 'as' => 'admin.' ], function () {
            //        roles
            Route::resource( '/role', 'Admin\User\RoleController', [ 'except' => [ 'show', 'create' ] ] );
            //        users
            Route::resource( '/user', 'Admin\User\UserController', [ 'except' => [ 'show' ] ] );
            Route::get( '/profile', 'Admin\User\UserController@profile' )->name( 'profile.index' );
            Route::patch( '/profile', 'Admin\User\UserController@profile_update' )->name( 'profile.update' );
            //        media
            Route::get( '/media', 'Admin\Media\MediaController@index' )->name( 'media.index' );
            Route::get( '/media/{id}/delete', 'Admin\Media\MediaController@delete' )->name( 'media.destroy' );
            Route::get( '/media/{id}/edit', 'Admin\Media\MediaController@edit' )->name( 'media.edit' );
            Route::patch( '/media/{id}', 'Admin\Media\MediaController@update' )->name( 'media.update' );
            Route::post( '/media', 'Admin\Media\MediaController@store' )->name( 'media.file_store' );
            Route::delete( '/media', 'Admin\Media\MediaController@delete' )->name( 'media.file_delete' );
            Route::patch( '/media', 'Admin\Media\MediaController@refresh' )->name( 'media.refresh_list' );
            //            language translation
            Route::get( '/string_translation/{lang?}', 'Admin\Lang\TranslationController@string_translation' )->name( 'string_translation.index' );
            Route::post( '/string_translation/update_string', 'Admin\Lang\TranslationController@update_string' )->name( 'string_translation.update_string' );
            //            themes
            Route::get( '/themes', 'Admin\Appearance\ThemeController@index' )->name( 'appearance.theme.index' );
            Route::get( '/theme_screenshot/{name}', 'Admin\Appearance\ThemeController@show_screenshot' )->name( 'appearance.theme.show_screen_shot' );
            Route::post( '/themes', 'Admin\Appearance\ThemeController@set_active' )->name( 'appearance.theme.set_active' );
            Route::patch( '/themes', 'Admin\Appearance\ThemeController@publish' )->name( 'appearance.theme.publish' );
            //        users
            Route::resource( '/page', 'Admin\Page\PageController', [ 'except' => [ 'show' ] ] );
            Route::resource( '/post', 'Admin\Post\PostsController', [ 'except' => [ 'show' ] ] );
            Route::resource( '/post_category', 'Admin\Post\PostCategoryController', [ 'except' => [ 'show' ] ] );
            Route::resource( '/task', 'Admin\Task\TaskController', [ 'except' => [ 'show' ] ] );
            Route::post( '/task/{id}/move', 'Admin\Task\TaskController@move' )->name('task.move');
        } );
    } );

/**
 * load page
 */
Route::get( '/{slug}', 'Admin\Page\PageController@show' );
