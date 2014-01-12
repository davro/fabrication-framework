<?php
namespace Fabrication\Tests;

//------------------------------------------------------------------------------
// Project.
//------------------------------------------------------------------------------
define('PROJECT_ROOT_DIR',			realpath(dirname(dirname(__FILE__))));
define('PROJECT_NAME',				'Project Testing');	# Display name.
define('PROJECT_DOMAIN',			'project-testing');
define('PROJECT_DATABASE',			'project-testing');

//------------------------------------------------------------------------------
// Framework.
//------------------------------------------------------------------------------
define('FRAMEWORK_ROOT_DIR',		realpath(PROJECT_ROOT_DIR . '/../project-fabrication-framework'));
//define('FRAMEWORK_ENVIRONMENT', 'dev');
//define('FRAMEWORK_ENVIRONMENT', 'prod');


include dirname(__FILE__) . '/../library/bootstrap.php';

use Library\Configuration;
use Library\Fabrication;


class FabricationTest extends \PHPUnit_Framework_TestCase {
	
	
	public function setUp() {
		
		// Create the framework instance.
		$this->fabric = Fabrication::createInstance();
		
		// Dispatch the framework instance.
		if (defined('FRAMEWORK_DISPATCHER')) {
			
			if (FRAMEWORK_DISPATCHER) {
				//$this->fabric->dispatch();
			}
		}
		
	} // end function setUp
	
	public function testObject() {
		
		$this->assertInternalType('object', $this->fabric);
		
	} // end function testObject
	
	public function testNamespace() {
		
		$this->assertInstanceOf('Library\Fabrication', $this->fabric);
		
	} // end function testNamespace
	
	public function testConfigurationFramework() {
		
		$configuration = Configuration::getAll();
		
		// Default setup.
		$this->assertEquals('default', Configuration::get('framework_current'));
		$this->assertEquals('dev', Configuration::get('framework_environment'));
		
	} // end function testConfigurationFramework
	
	public function testConfigurationProject() {
		
		$configuration = Configuration::getAll();
		$this->assertEquals('standalone',  $configuration['project']['name']);
		
	} // end function testConfigurationProject
	
//	public function testService() {
//
////		$uri	= 'http://localhost/';
////		$title	= 'Fabrication Title';
//		
////		$uri	= 'http://www.bing.com/';
////		$title	= 'Bing';
//
////		$uri	= 'http://rss.slashdot.org/Slashdot/slashdot';
////		$title	= '';
////		
//		$uri = 'http://daringfireball.net/index.xml';
//		
//		$this->assertTrue($this->fabric->registerService($uri));
//		
////		$service = $this->fabric->retriveService($uri);
//		
////		$this->assertInternalType('object', $service);
////		$this->assertEquals($title, $service->getTitle()->item(0)->nodeValue);
//		//$this->assertEquals($title, $service->saveHTML());
//	}

} // end class