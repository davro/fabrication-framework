<?php
namespace Fabrication\Tests;

use Library\Fabrication;
use Library\DataEntity;


class LibraryDataEntityTest extends \PHPUnit_Framework_TestCase {
	
	
	public function setUp() {

		$this->engine = Fabrication::createInstance('engine');
		
		$this->entity = DataEntity::create();
	}
	
	public function testInstance() {
		
		$this->assertInternalType('object', $this->entity);
		$this->assertInstanceOf('Library\DataEntity', $this->entity);
	}
	
	// testing
	function innerHTML($element) { 
		
		$innerHTML = ''; 
		
		$children = $element->childNodes; 
		
		foreach ($children as $child) {
			$tmp_dom = new \DOMDocument(); 
			$tmp_dom->appendChild($tmp_dom->importNode($child, true)); 
			$innerHTML.=trim($tmp_dom->saveHTML()); 
		} 
		return $innerHTML; 
	}
	
}
