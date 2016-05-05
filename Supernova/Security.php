<?php

namespace Supernova;

class Security
{
    /**
     * Regex labels to clean
     * @var array
     */
    private static $labels = array(
        '@<script[^>]*?>.*?</script>@si',
        '@&#(\d+);@',
        '@\[\[(.*?)\]\]@si',
        '@\[!(.*?)!\]@si',
        '@\[\~(.*?)\~\]@si',
        '@\[\((.*?)\)\]@si',
        '@{{(.*?)}}@si',
        '@\[\+(.*?)\+\]@si',
        '@\[\*(.*?)\*\]@si'
    );

    /**
     * Global vars names
     * @var array
     */
    private static $globals = array(
        '_SESSION',
        '_POST',
        '_GET',
        '_COOKIE',
        '_REQUEST',
        '_SERVER',
        '_ENV',
        '_FILES'
    );

    /**
     * Clean all requests
     * @return null
     */
    public static function cleanAll()
    {
        if (isset($_SERVER['QUERY_STRING']) && strpos(urldecode($_SERVER['QUERY_STRING']), chr(0)) !== false) {
            \Supernova\View::setError(500);
            trigger_error();
            return;
        }
        self::unregisterGlobals();
        self::cleanAllVars();
    }

    /**
     * Clean each target
     * @param  string  $target  Target to clean
     * @param  integer $limit   [description]
     * @return [type]           [description]
     */
    private static function clean($target = '', $limit = 3)
    {
        foreach ($target as $key => $value) {
            if (is_array($value) && $limit > 0) {
                self::clean($value, $limit - 1);
            } else {
                $target[$key] = preg_replace_callback(self::$labels, "self::setEmpty", $value);
            }
        }
        return $target;
    }

    /**
     * Return empty string
     * @param string $matches Matches
     */
    private static function setEmpty($matches)
    {
        return "";
    }

    /**
     * Clean all vars in memory
     * @return null
     */
    private static function cleanAllVars()
    {
        foreach (array($_GET, $_POST, $_COOKIE, $_REQUEST) as $eachClean) {
            self::clean($eachClean);
            self::sanitize($eachClean);
        }
        foreach (array('PHP_SELF', 'HTTP_USER_AGENT', 'HTTP_REFERER', 'QUERY_STRING') as $key) {
            $_SERVER[$key] = (isset($_SERVER[$key])) ? htmlspecialchars($_SERVER[$key], ENT_QUOTES) : null;
        }
    }

    /**
     * Sanitize data from values
     * @param  string $value Value to clean
     * @return string        Cleaned value
     */
    public static function sanitize($value = '')
    {
        return is_array($value) ? array_map('self::sanitize', $value) : stripslashes(trim($value));
    }

    /**
     * Unregisted any Globals in memory
     * @return null
     */
    private static function unregisterGlobals()
    {
        if (ini_get('register_globals')) {
            foreach (self::$globals as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }
}
