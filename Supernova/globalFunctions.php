<?php
/**
 * Depurador : muestra depuración de variables en el navegador
 * @param  mixed $str Arreglo o string
 * @return null
 */
function debug($str)
{
    \Supernova\Debug::render($str);
}

/**
 * Función para traducción de texto
 * @param  string $str String original
 * @return string      String reemplazado
 */
function __($str)
{
    return \Supernova\Translate::text($str);
}

/**
 * Inyecta variables a un string
 * @param  string $str  texto
 * @param  array  $vars variables a reemplazar en el string
 * @return string       string inyectado
 */
function inject($str = "", $vars = array(), $char = '%')
{
    foreach ((array)$vars as $k => $v) {
        $str = str_replace($char.$k.$char, $v, $str);
    }
    return $str;
}

/**
 * Start console session in browser
 * @return void
 */
function consoleStart()
{
    \Supernova\Console::start();
}

/**
 * Send console message to browser
 * @param  string $message Text
 * @return void
 */
function consoleLog($message = '')
{
    \Supernova\Console::log($message);
}

/**
 * Register shutdown function
 * @return void
 */
function shutdownFunction()
{
    \Supernova\Profiler::end();
    \Supernova\Profiler::show();
    $error = error_get_last();
    if (!is_null($error)) {
        \Supernova\View::setError(500);
        \Supernova\Debug::renderError($error);
        \Supernova\View::callError(\Supernova\Debug::$encodedError);
    } elseif (\Supernova\View::$errorNumber != 0) {
        \Supernova\View::callError(\Supernova\Debug::$encodedError);
    }
}

/**
 * Register error handler function
 * @param  int    $errno   Error number
 * @param  string $errstr  Error message
 * @param  string $errfile Error file
 * @param  string $errline Error line
 * @return void
 */
function errorHandler($errno, $errstr, $errfile, $errline)
{
    $error = array("type" => $errno, "message" => $errstr, "file" => $errfile, "line" => $errline);
    \Supernova\View::setError(500);
    \Supernova\Debug::renderError($error);
    die();
}
