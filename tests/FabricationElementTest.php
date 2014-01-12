<?php
namespace Fabrication\Tests;

use Library\Fabrication;
use Library\FabricationElement;


class FabricationElementTest extends \PHPUnit_Framework_TestCase {
	
	
	public function setUp() {

		$this->engine = Fabrication::createInstance('engine');
		
		$this->element = new FabricationElement($this->engine);
	}
	
	public function testInstance() {
		
		$this->assertInternalType('object', $this->element);
		$this->assertInstanceOf('Library\FabricationElement', $this->element);
	}
	
	
	public function testControlGroup() {
		
		$controlGroup = $this->element->controlGroup();
				
		$this->assertEquals('div', $controlGroup->nodeName);
		$this->assertEquals('', $controlGroup->nodeValue);
		
		// Attributes.
		$nodeMap = $controlGroup->attributes;
		$attributeClass = $nodeMap->getNamedItem('class');
		
		$this->assertInstanceOf('DOMNamedNodeMap', $nodeMap);
		$this->assertInstanceOf('DOMAttr', $attributeClass);
		$this->assertEquals('control-group', $attributeClass->nodeValue);
		
		// Children
		$childNodes = $controlGroup->childNodes;
		
		// Children empty default node must be added by DOMDocument.
		$this->assertInstanceOf('DOMNodeList', $childNodes);
		$this->assertEquals(1, count($childNodes->length));
//		$this->assertEquals('#text', $childNodes->item(0)->nodeName);
//		$this->assertEquals('', $childNodes->item(0)->nodeValue);
		
	}
	
	public function testControlGroupAddingControl() {
		
		$controlGroup = $this->element->controlGroup();
		$controlGroup->appendChild($this->engine->create('div', 'TEST'));
		
		// Root element.
		$this->assertEquals('div', $controlGroup->nodeName);
		$this->assertEquals('TEST', $controlGroup->nodeValue);
		
		// Attributes.
		$nodeMap = $controlGroup->attributes;
		$attributeClass = $nodeMap->getNamedItem('class');
		
		$this->assertInstanceOf('DOMNamedNodeMap', $nodeMap);
		$this->assertInstanceOf('DOMAttr', $attributeClass);
		$this->assertEquals('control-group', $attributeClass->nodeValue);
		
//		$this->engine->dump($controlGroup);
		
//		// Children
//		$childNodes = $controlGroup->childNodes;
//		
//		// Children default node must be added by DOMDocument.
//		$this->assertInstanceOf('DOMNodeList', $childNodes);
//		$this->assertEquals(1, count($childNodes->length));
//		$this->assertEquals('#text', $childNodes->item(0)->nodeName);
//		$this->assertEquals('', $childNodes->item(0)->nodeValue);
		
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