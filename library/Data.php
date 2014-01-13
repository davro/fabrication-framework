<?php
namespace Library;

/*
 * This file is part of the fabrication framework.
 * 
 * David Stevens <mail.davro@gmail.com> <davro@davro.net>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this version of the source code.
 */

/**
 * Data Abstraction Layer.
 *
 * @package		Fabrication
 * @subpackage	Data
 * @author		David Stevens <mail.davro@gmail.com>
 * 
 * @note Data
 */
class Data {
	
	protected static $object;

	/**
	 * Creation method.
	 * 
	 * @return	object	Data
	 */
	public static function create() {
		
		$calledClass = get_called_class();
		
		self::$object = new $calledClass();
		
		return self::$object;
		
	} // end function create
	
} // end class Data
