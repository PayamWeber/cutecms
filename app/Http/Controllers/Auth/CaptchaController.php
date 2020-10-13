<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\CaptchaHelper;
use App\Http\Controllers\Controller;

class CaptchaController extends Controller
{

	/**
	 * this method will show the image for browser and save the code in session
	 */
	public function show()
	{
		$char = strtoupper( substr( str_shuffle( 'abcdefghjkmnpqrstuvwxyz' ), 0, 4 ) );
		$str  = rand( 1, 7 ) . rand( 1, 7 ) . rand( 1, 7 );
		\Session::flash( 'Captcha', $str );
		\Session::save();

		$bg_file = public_path( '/public/assets/captcha/bg.png' );
		if ( ! isset( $bg_file ) )
		{
			die();
		}

		$image = imagecreatefrompng( $bg_file );

		$colour = imagecolorallocate( $image, 56, 56, 56 );

		// font file path
		$font = public_path( '/public/assets/captcha/mtwfont.ttf' );

		// if we can use custom font then use it otherwise use the simple font
		if ( function_exists( 'imagettftext' ) && file_exists( $font ) )
		{
			$rotate = rand( -3, 5 );
			imagettftext( $image, 22, $rotate, 25, 30, $colour, $font, $str );
		} else
		{
			$rotateX = rand( 1, 78 );
			$rotateY = rand( 1, 28 );
			imagestring( $image, 15, $rotateX, $rotateY, $str, $colour );
		}

		header( 'Content-type: image/png' );
		header( 'Cache-control: no-cache' );
		imagepng( $image );

		die();
	}
}
