<?php

namespace Fabrication;

/*
 * This file is part of the fabrication framework.
 * 
 * David Stevens <mail.davro@gmail.com> <davro@davro.net>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this version of the source code.
 */

/**
 * Configuration stores data.
 *
 * @package     Fabrication
 * @subpackage  Configuration
 * @author      David Stevens <mail.davro@gmail.com>
 */
class Configuration
{
    
    /**
     * The configuration structure array.
     *
     * @var array
     */
    protected static $structure = array();

    /**
     * Retrieves a configuration parameter.
     *
     * @param   string  $name       A configuration parameter name.
     * @param   mixed   $default    A default configuration parameter value.
     * @return  mixed   A configuration parameter value, if the configuration parameter exists, otherwise null
     */
    public static function get($name, $default = null)
    {
        return isset(self::$structure[$name]) ? self::$structure[$name] : $default;
    }
    
    /**
     * Indicates whether or not a configuration parameter exists.
     *
     * @param string $name A configuration parameter name
     * @return bool true, if the configuration parameter exists, otherwise false
     */
    public static function has($name)
    {
        return array_key_exists($name, self::$structure);
    }
    
    /**
     * Sets a configuration parameter.
     *
     * If a configuration parameter with the name already exists the value will be overridden.
     *
     * @param string $name  A configuration parameter name
     * @param mixed  $value A configuration parameter value
     */
    public static function set($name, $value)
    {
        self::$structure[$name] = $value;
    }
    
    /**
     * Sets an array of configuration parameters.
     *
     * If an existing configuration parameter name matches any of the keys in
     * the supplied array, the associated value will be overridden.
     *
     * @param array $parameters An associative array of configuration parameters and their associated values
     */
    public static function add($parameters = array())
    {
        self::$structure = array_merge(self::$structure, $parameters);
    }
    
    /**
     * Retrieves all configuration parameters.
     *
     * @return array An associative array of configuration parameters.
     */
    public static function getAll()
    {
        return self::$structure;
    }
    
    /**
     * Clears all current config parameters.
     *
     * @return void
     */
    public static function clear()
    {
        self::$structure = array();
    }
}
