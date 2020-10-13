<?php

namespace App\Helpers;

use Session;
use SmartUI;

class AlertHelper
{
    CONST SESSION_NAME = 'alert_message';

    /**
     * @param string $message
     * @param string $type
     *
     * @return bool
     */
    public static function make( $message = "", $type = "success" )
    {
        if ( ! $message )
            return false;
        if ( ! $type )
            $type = 'success';
        $new_value = [
            [
                'message' => $message,
                'type' => $type,
            ],
        ];
        if ( session( self::SESSION_NAME ) )
            $new_value = array_merge( session( self::SESSION_NAME ), $new_value );
        Session::flash( self::SESSION_NAME, $new_value );
    }

    public static function get()
    {
        return session( self::SESSION_NAME );
    }

    public static function print( $message, $type = 'info' )
    {
        return SmartUI::print_alert( $message, $type );
    }
}
