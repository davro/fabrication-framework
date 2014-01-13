<?php
namespace Fabrication\Tests;

use Library\Fabrication;
use Library\Data;


class DataTest extends \PHPUnit_Framework_TestCase {
	
	
	public function setUp() {

		$this->engine = Fabrication::createInstance('engine');
		
		$this->data = Data::create();
	}
	
	public function testInstance() {
		
		$this->assertInternalType('object', $this->data);
		$this->assertInstanceOf('Library\Data', $this->data);
	}
	
}
