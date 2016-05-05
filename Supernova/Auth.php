<?php

namespace Supernova;

/**
 * Autentificación de usuarios
 */
class Auth
{
    /**
     * Crea una variable de sesion "AuthUser" con los datos encriptados del usuario
     * @param array $data Datos del usuario
     */
    public static function setUser($data)
    {
        \Supernova\Session::create('AuthUser', \Supernova\Crypt::encrypt($data));
    }

    /**
     * Obtiene los datos del usuario desde la variable de sesion
     * @return mixed Devuelve el usuario o false
     */
    public static function getUser()
    {
        $data = \Supernova\Session::read('AuthUser');
        if ($data) {
            $User = \Supernova\Crypt::decrypt($data);
            return $User;
        }
        return false;
    }

    /**
     * Elimina la sesion del usuario
     * @return null
     */
    public static function unsetUser()
    {
        \Supernova\Session::destroy('AuthUser');
    }

    /**
     * Crea el hash para la contraseña ingresada
     * @param  string $password Contraseña
     * @return string           Contraseña encriptada
     */
    public static function hash($password)
    {
        if (function_exists('password_hash')) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
        } else {
            $salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
            $salt = base64_encode($salt);
            $salt = str_replace('+', '.', $salt);
            $hash = crypt($password, '$2y$10$' . $salt . '$');
        }
        return $hash;
    }

    /**
     * Verifica contraseña ingresada con una con hash
     * @param  string $password Contraseña
     * @param  string $hash     Contraseña encriptada
     * @return boolean           true o false
     */
    public static function verifyHash($password, $hash)
    {
        if (function_exists('password_verify')) {
            $boolReturn = password_verify($password, $hash);
        } else {
            $strHash2 = crypt($password, $hash);
            $boolReturn = $hash == $strHash2;
        }
        return $boolReturn;
    }
}
