<?php
namespace Fabrication\Tests;

use Fabrication\Fabrication;


class ApplicationTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
    {
		$this->engine = Fabrication::createInstance('engine');	
	}
	
	public function testInstance()
    {	
	}
}
