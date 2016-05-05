<?php

namespace Supernova;

class Profiler
{
    /**
     * Profiler start time
     * @var int
     */
    private static $startTime;

    /**
     * Profiler end time
     * @var int
     */
    private static $endTime;

    /**
     * Start profiler
     * @return null
     */
    public static function start()
    {
        self::$startTime = microtime(true);
    }

    /**
     * Stop profiler
     * @return null
     */
    public static function end()
    {
        self::$endTime = microtime(true);
    }

    /**
     * Show profile
     * @return null
     */
    public static function show()
    {
        if (!defined("ENVIRONMENT") || ENVIRONMENT == "dev") {
            $performance = self::$endTime - self::$startTime;
            echo self::styleTime();
            echo "<div class='profiler'>".
            "Supernova Framework &copy; 2014 :: ".__("Profiler")." :: ".//"<br/>".
            __("Performance").": ".number_format($performance, 4)." ".__("seconds")." :: ".//"<br/>".
            __("Memory allocated").": ".round(memory_get_usage()/1024)."Kb :: ".//"<br/>".
            __("Memory peak").": ".round(memory_get_peak_usage()/1024)."Kb"."<br/>".
            \Supernova\Debug::showQuery().
            "</div>";
        }
    }

    /**
     * Style for profiler
     * @return null
     */
    private static function styleTime()
    {
        $output = '<style>';
        ob_start();
        include CORE.'css'.DS.'profiler.css';
        $content = ob_get_contents();
        ob_end_clean();
        $output.= $content;
        $output.= '</style>';
        return $output;
    }
}
