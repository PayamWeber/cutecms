<?php

namespace App\Providers;

use App\Models\Option;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$active_theme       = Option::get_option( 'active_theme' );

		if ( ! $active_theme || ( $active_theme && ! file_exists( $active_theme['path'] . 'index.blade.php' ) ) )
		{
			$active_theme = include( resource_path( 'samples/theme/info.php' ) );
			copy_directory( resource_path( 'samples/theme' ), resource_path('themes/default') );

			$active_theme = [
				'name' => 'default',
				'path' => resource_path('themes/default'),
				'info' => $active_theme,
			];
			update_option( 'active_theme', json_encode( $active_theme, JSON_UNESCAPED_UNICODE ), true );
		}

		$GLOBALS[ 'theme' ] = $active_theme;

		$this->loadViewsFrom( $active_theme[ 'path' ], 'theme' );
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
	}
}
