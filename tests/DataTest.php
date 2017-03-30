<?php
namespace Fabrication\Tests;

use Fabrication\Data;

class DataTest extends \PHPUnit_Framework_TestCase
{
	public function testInstance()
    {
		$data = Data::create();
		$this->assertInternalType('object', $data);
		$this->assertInstanceOf('Fabrication\Data', $data);
	}
}
