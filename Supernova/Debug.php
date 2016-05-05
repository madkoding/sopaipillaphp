<?php

namespace Supernova;

class Debug
{
    /**
     * Log querys from SQL
     * @var array
     */
    private static $logQuery = array();
    
    /**
     * Encoded error
     * @var string
     */
    public static $encodedError;

    public static $errorColor = "#FFFFCC";

    /**
     * Error types
     * @var array
     */
    private static $errorType = array(
        E_ERROR              => 'Error',
        E_WARNING            => 'Warning',
        E_PARSE              => 'Parsing Error',
        E_NOTICE             => 'Notice',
        E_CORE_ERROR         => 'Core Error',
        E_CORE_WARNING       => 'Core Warning',
        E_COMPILE_ERROR      => 'Compile Error',
        E_COMPILE_WARNING    => 'Compile Warning',
        E_USER_ERROR         => 'User Error',
        E_USER_WARNING       => 'User Warning',
        E_USER_NOTICE        => 'User Notice',
        E_STRICT             => 'Runtime Notice',
        E_RECOVERABLE_ERROR  => 'Catchable Fatal Error',
        8192                 => 'Unknown'
    );

    /**
     * Show debux box
     * @param  mixed $str String or array with values
     */
    public static function render($str)
    {
        if (!defined('ENVIRONMENT') || ENVIRONMENT == 'dev') {
            $backtrace = debug_backtrace();
            $firstTrace = $backtrace[0];
            $secondTrace = $backtrace[1];
            $file = str_replace(ROOT.DS, '', $secondTrace['file']);
            $line = $secondTrace['line'];
            $object = "";
            if (isset($secondTrace['object']) && is_object($secondTrace['object'])) {
                $object = get_class($object);
            }
            self::$errorColor = "#CFF";
            echo self::style();
            echo self::drawDebugBox($object, $line, $file, $str);
        }
    }

    /**
     * Show error box
     * @param  array $error Array with error info
     * @return string        HTML Box
     */
    public static function renderError($error)
    {
        if (!empty($error)) {
            $backtrace = debug_backtrace();
            if (isset($backtrace[2]) && $backtrace[2]['function'] == 'trigger_error') {
                if (isset($backtrace[3]['file'])) {
                    $error['line'] = $backtrace[3]['line'];
                    $error['file'] = $backtrace[3]['file'];
                } else {
                    $error['line'] = $backtrace[2]['line'];
                    $error['file'] = $backtrace[2]['file'];
                }
            }
            extract($error);
            if (!defined('ENVIRONMENT') || ENVIRONMENT == 'dev') {
                self::$errorColor = (in_array($type, array(E_ERROR, E_CORE_ERROR, E_USER_ERROR))) ? '#F6D8CE' : '#FFFFCC';
                echo self::style();
                echo self::drawDebugBox($type, $line, $file, $message);
            } elseif (ob_get_contents()) {
                ob_end_clean();
            }
            preg_match("/(Supernova)/i", $file, $matches);
            if (empty($matches)) {
                if ($line) {
                    $br = "\n";
                    $text = $type.$br.$line.$br.$file.$br.$message.$br.implode($br, self::getLines($file, $line));
                    self::$encodedError = \Supernova\Crypt::encrypt($text);
                }
            }
            //$encodedError = \Supernova\Crypt::decrypt($encodedError);
        }
    }
    
    /**
     * Draw debug dialog
     * @param  string $object Object name
     * @param  integer $line  Error line
     * @param  string $file   Error file
     * @param  string $str    Error message
     */
    private static function drawDebugBox($object, $line, $file, $str = "")
    {
        $objects = (isset(self::$errorType[$object])) ? __(self::$errorType[$object]) : __($object);
        $str = print_r($str, true);
        $lineStr = __('Line');
        $fileStr = __('File');
        $fullfile = $file;
        $file = str_replace(ROOT.DS, '', $file);
        preg_match("/(Supernova)/i", $file, $matches);
        if ($file != 'index.php' && empty($matches)) {
            $output = "<div class='debug-box' style='background-color: ".self::$errorColor."'><h3>$objects</h3>";
            $output.= ($line) ? "<p>$lineStr <strong>$line</strong> :: $fileStr <strong>$file</strong></p>" : "";
            $output.= (isset(self::$errorType[$object])) ? "<h5>$str</h5>" : "<pre>$str</pre>";
            $output.= (isset(self::$errorType[$object])) ? self::showLines(self::getLines($fullfile, $line), $line) : "";
            $output.= "</div>";
        } else {
            $output = "<div class='debug-box'><pre>$str</pre></div>";
        }

        return $output;
    }

    /**
     * Style for debug box
     * @param  string $errorColor Hexadecimal color for box
     * @return string             CSS style
     */
    private static function style($errorColor = '#FFC')
    {
        $output = '<style>';
        ob_start();
        include CORE.'css'.DS.'debug.css';
        $content = ob_get_contents();
        ob_end_clean();
        $output.= $content;
        $output.= '</style>';
        return $output;
    }

    /**
     * Save SQL Log
     * @param  string $query SQL Query
     */
    public static function logQuery($query = '')
    {
        $backtrace = debug_backtrace();
        $class = (isset($backtrace[2]['class'])) ? $backtrace[2]['class'] : '';
        $method = $backtrace[2]['function'].'()->'.$backtrace[1]['function'].'()';
        $line = (isset($backtrace[1]['line'])) ? $backtrace[1]['line'] : '';
        self::$logQuery[$class.'->'.$method.' '.__('Line').':'.$line] = $query;
    }

    /**
     * Show SQL log
     * @return String SQL Querys HTML box
     */
    public static function showQuery()
    {
        $output ='<table style="font-family: Monaco, monospace; font-size: 9px; width:750px; text-align:left; margin: 5px auto;">';
        foreach (self::$logQuery as $where => $query) {
            $output.="<tr>
            <td style='color: white; border-bottom: 1px dotted black; padding: 3px'>
                $where
            </td>
            <td style='color:lightgreen; border-bottom: 1px dotted black; padding: 3px'>
                $query
            </td></tr>";
        }
        $output.='</table>';
        return $output;
    }

    /**
     * Get lines for debug
     * @param  string  $file   Filename
     * @param  integer $line   Line from filename
     * @param  integer $expand Quantity of lines
     */
    private static function getLines($file, $line, $expand = 4)
    {
        if ($file) {
            $source = file($file);
            $body = array_slice($source, $line-$expand, $expand*2);
            return $body;
        }
        return "";
    }

    /**
     * Show lines from file in debug
     * @param  string  $body   Body file
     * @param  integer $line   Target line number
     * @param  integer $expand Quantity of lines
     */
    private static function showLines($body, $line = '', $expand = 4)
    {
        $output = "";
        if ($line) {
            $output.= '<pre>';
            $output.= __('Line')."\t".__('Source')."\n";
            $output.= '<hr/>';
            $counter = 1;
            foreach ($body as $eachLine) {
                $linenum = $line - $expand + $counter++;
                $checkLine = ($linenum >= $line-1 && $linenum <= $line+1) ? 'checkLine' : '';
                $checkError = ($linenum == $line) ? 'checkError' : '';
                $output.="<div class='$checkLine $checkError'>$linenum"."\t".htmlentities($eachLine)."</div>";
            }
            $output.= '</pre>';
        }
        return $output;
    }
}
