<?php

namespace App\Http\Controllers\Admin\Appearance;

use App\Helpers\AlertHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThemeController extends Controller
{

	/**
	 * @var array all themes inside this variable
	 */
	private $themes = [];

	public $data = [];

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		//
		$themes_path = resource_path( 'themes/' );

		// register and locate themes
		$this->locate_themes( $themes_path );

		$this->data[ 'themes' ] = $this->themes;

		return view( 'admin.appearance.theme.index', $this->data );
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	public function show_screenshot( $name )
	{
		$path      = resource_path( 'themes/' . $name . '/' );
		$mime_type = 'image/jpg';

		if ( file_exists( $path . 'screenshot.jpg' ) )
		{
			$path = $path . 'screenshot.jpg';
		} else if ( file_exists( $path . 'screenshot.png' ) )
		{
			$path      = $path . 'screenshot.png';
			$mime_type = 'image/png';
		} else
		{
			return '';
		}
		header( 'Content-Type: ' . $mime_type );

		readfile( $path );
		die();
	}

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function set_active( Request $request )
	{
		$request->validate( [
			'name' => 'required',
		] );
		$errors      = [];
		$themes_path = resource_path( 'themes/' );
		$this->locate_themes( $themes_path );

		if ( ! isset( $this->themes[ $request->get( 'name' ) ] ) )
			$errors[] = lang( 'Theme Not Exists' );

		if ( $errors )
		{
			foreach ( $errors as $error )
			{
				AlertHelper::make( $error, 'danger' );
			}
			return back()->withInput();
		} else
		{
			$active_theme = update_option( 'active_theme', json_encode( $this->themes[ $request->get( 'name' ) ], JSON_UNESCAPED_UNICODE ), true );
			if ( $active_theme )
			{
				AlertHelper::make( lang( 'Operation successful' ) );
				return back()->withInput();
			} else
			{
				AlertHelper::make( lang( 'there\'s a problem with saving data, Please try again' ), 'danger' );
				return back()->withInput();
			}
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function publish( Request $request )
	{
		$request->validate( [
			'name' => 'required',
		] );
		$errors      = [];
		$themes_path = resource_path( 'themes/' );
		$this->locate_themes( $themes_path );

		if ( ! isset( $this->themes[ $request->get( 'name' ) ] ) )
			$errors[] = lang( 'Theme Not Exists' );

		if ( $errors )
		{
			foreach ( $errors as $error )
			{
				AlertHelper::make( $error, 'danger' );
			}
			return back()->withInput();
		} else
		{
			$theme = $this->themes[ $request->get( 'name' ) ];

			rmdirr( public_path( 'public/themes/' . $theme[ 'name' ] ) );
			copy_directory( $theme[ 'path' ], public_path( 'public/themes/' . $theme[ 'name' ] ) );
			$this->remove_php_files( public_path( 'public/themes/' . $theme[ 'name' ] ) );

			if ( true )
			{
				AlertHelper::make( lang( 'Operation successful' ) );
				return back()->withInput();
			} else
			{
				AlertHelper::make( lang( 'there\'s a problem with saving data, Please try again' ), 'danger' );
				return back()->withInput();
			}
		}
	}

	/**
	 * @param $path
	 */
	private function locate_themes( $path )
	{
		$files = scandir( $path );
		unset( $files[ 0 ], $files[ 1 ] );

		if ( $files )
		{
			foreach ( $files as $file )
			{
				if ( is_dir( $path . $file ) )
				{
					$theme_path = $path . $file . '/';

					if ( ! file_exists( $theme_path . 'index.blade.php' ) )
						continue;

					$default_theme_info = [
						'theme_name' => lang( 'Unknown' ),
						'theme_author' => lang( 'Unknown' ),
						'theme_author_url' => '',
						'description' => '',
					];
					$theme_info         = $default_theme_info;

					if ( file_exists( $theme_path . 'info.php' ) )
					{
						$theme_info = include( $theme_path . 'info.php' );
						$theme_info = is_array( $theme_info ) ? array_merge( $default_theme_info, $theme_info ) : [];
					}

					$this->themes[ $file ] = [
						'name' => $file,
						'path' => $theme_path,
						'info' => $theme_info,
					];
				}
			}
		}
	}

	/**
	 * @param $path
	 */
	private function remove_php_files( $path )
	{
		$files = scandir( $path );
		unset( $files[ 0 ], $files[ 1 ] );

		if ( $files )
		{
			foreach ( $files as $file )
			{
				if ( is_dir( $path . '/' . $file ) )
				{
					$this->remove_php_files( $path . '/' . $file );
				} else if ( mb_substr( $file, strlen( $file ) - 4, 4 ) == '.php' )
				{
					unlink( $path . '/' . $file );
				}
			}
		}
	}
}
