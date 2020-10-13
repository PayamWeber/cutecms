<?php
/**
 * Created by PhpStorm.
 * User: PMW
 * Date: 3/29/2019
 * Time: 10:04 PM
 */

namespace App\Helpers;

class CryptHelper
{
	const LOGIN_SECRET_PASSCODE = 'clmnXnsHVqQo6PNptZM';

	/**
	 * @param $text
	 * @param $secret_passcode
	 *
	 * @return string
	 */
	public static function encrypt_aes_256( $text, $secret_passcode )
	{
		$method = 'aes-256-cbc';

		$key = substr( hash( 'sha256', $secret_passcode, true ), 0, 32 );

		$iv = chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 );

		$encrypted = base64_encode( openssl_encrypt( $text, $method, $key, OPENSSL_RAW_DATA, $iv ) );

		return $encrypted;
	}

	/**
	 * @param $encrypted
	 * @param $secret_passcode
	 *
	 * @return string
	 */
	public static function decrypt_aes_256( $encrypted, $secret_passcode )
	{
		$method = 'aes-256-cbc';

		$key = substr( hash( 'sha256', $secret_passcode, true ), 0, 32 );

		$iv = chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 ) . chr( 0x0 );

		$encrypted = str_replace( ' ', '+', $encrypted );
		$decrypted = openssl_decrypt( base64_decode( $encrypted ), $method, $key, OPENSSL_RAW_DATA, $iv );

		return $decrypted;
	}
}