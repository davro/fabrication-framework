<?php
namespace Library;

use Library\Data;

/*
 * This file is part of the fabrication framework.
 * 
 * David Stevens <mail.davro@gmail.com> <davro@davro.net>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this version of the source code.
 */

/**
 * Data entity
 *
 * @package		Library
 * @subpackage	DataEntity
 * @author		David Stevens <mail.davro@gmail.com>
 * 
 */
class DataEntity extends Data implements \ArrayAccess {
	
	public static $name;
	
	public static $namespace;
	
	public function __construct() {
		
	} // end function __construct
	
	/**
	 * Magic setter.
	 * 
	 * @param	mixed	$key
	 * @param	mixed	$value
	 * @return	boolean
	 */
    public function __set($key, $value) {
		
		$this->$key = (object) $value;
			
		return true;
		
    } // end function __set
	
	/**
	 * Setter for changing the object name.
	 * 
	 * @param	string	$name
	 * @return	boolean
	 */
	public function	setName($name) {
		
		if (! $name) { return; }
		
		self::$name = $name;
		return true;
		
	} // end function setName
	
	/**
	 * Getter for retriving the object name.
	 * 
	 * @return type
	 */
	public function	getName() {
		
		return self::$name;
		
	} // end function setName

	/**
	 * Setter for changing the namespace.
	 * 
	 * @param	string	$name
	 * @return	boolean
	 */
	public function	setNamespace($name) {
		
		if (! $name) { return; }
		
		self::$namespace = $name;
		return true;
		
	} // end function setNamespace
	
	/**
	 * Getter for retriving the namespace in string form \ replaced with -
	 * 
	 * @return type
	 */
	public function	getNamespace() {
		
		$namespace = strtolower(
			str_replace(array('\\'),array('-'), self::$namespace)
		);
		
		return $namespace;
		
	} // end function getNamespace
	
	/**
	 * ArrayAccess interface 
	 * 
	 * @todo Needs testing array interface.
	 * 
	 * @param type $name
	 * @return type
	 */
	public function __get($name) {
		
		return $this->$name;
		
	} // end function __get

	public function offsetGet($offset) {
		
		return $this->get($offset);
		
	} // end function offsetGet

	public function offsetSet($offset, $value) {
		
		$this->set($offset, $value);
		
	} // end function offsetSet
	
	public function offsetExists($offset) {
		
		return isset($this[$offset]);
		
	} // end function offsetExists
	
	public function offsetUnset($offset) {
		
		unset($this[$offset]);
		
	} // end function offsetUnset
	
} // end class DataEntity