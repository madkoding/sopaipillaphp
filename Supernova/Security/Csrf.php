<?php

namespace Supernova;

use \Supernova\Crypt as Crypt;

class Csrf extends \Supernova\Security
{
    /**
     * Generate CSRF Token
     * @return string CSRF Token
     */
    public static function generateToken()
    {
        return Crypt::encrypt(time());
    }

    /**
     * Check valid Csrf token in
     */
    /**
     * Check valid CSRF token with max of minutes
     * @param  string  $token CSRF Token
     * @param  integer $min   Minutes to be valid
     * @return boolean        Returns true or false
     */
    public static function isValidToken($token, $min = 5)
    {
        $token = Crypt::decrypt($token);
        $timeLimitBetween = array( time() , time()-(3600*$min) );
        return ($token < $timeLimitBetween[0] && $token > $timeLimitBetween[1]) ? true : false;
    }
}
