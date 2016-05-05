<?php

namespace Supernova\Blackhole;

class Files extends \Supernova\Blackhole\Generator
{
    /**
     * Check for writable path in the app
     * @param  string $path Path name
     * @return null
     */
    public static function checkWritablePath($path)
    {
        self::createFolder($path);
        if (!is_writable($path)) {
            trigger_error(
                __("Folder App not writable, check folder permissions (www-data/apache/nginx only should write)"),
                E_USER_ERROR
            );
        }
    }

    /**
     * Create folder if does not exist
     * @param  string $path Path
     * @return null
     */
    private static function createFolder($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
    
    /**
     * Create file if does not exist
     * @param  string $file Filename
     * @param  string $name Template name
     * @return null
     */
    public static function createIfNotExist($file, $name)
    {
        if (!file_exists($file)) {
            touch($file);
            chmod($file, 0777);
            file_put_contents($file, self::getTemplate($name));
        }
    }
    
    /**
     * Get template from templates path
     * @param  string $name Template name
     * @return string       HTML Template
     */
    private static function getTemplate($name)
    {
        $templateName = 'template'.$name;
        ob_start();
        include(Generator::$params["templatesPath"].DS.$templateName.".php");
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}
