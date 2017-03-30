<?php

namespace Fabrication;

/**
 *
 * Logger
 *
 * Example
 * Logger::instance()->log('your message here');
 *
 **/
final class Logger
{
    private static $instance;

    private function __construct()
    {
    }
    
    /**
     * Getter for retriving a Logger instance
     * 
     * @return Logger
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    /**
     * Prevent cloning of the instance.
     * 
     * @throws Exception
     */
    public function __clone()
    {
        throw new Exception('Cannot clone the logger object.');
    }
    
    /**
     * Add a message to the log
     * 
     * @param string $message
     */
    public function log($message = '')
    {
        $filename = PROJECT_ROOT_DIR.'/cache/log.txt';
        
        $file = @fopen($filename, 'a+');
        if ($file) {
            fwrite($file, $message."\r\n");
            fclose($file);
        }
    }
}
