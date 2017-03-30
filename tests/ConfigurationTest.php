<?php
namespace Fabrication\Tests;

use Fabrication\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
	public function testConfigurationFramework()
	{			
		$this->assertEquals('default', Configuration::get('framework_current'));
		$this->assertEquals('dev', Configuration::get('framework_environment'));	
	}
	
	public function testConfigurationProject()
	{	
		$configuration = Configuration::getAll();
		$this->assertEquals('standalone',  $configuration['project']['name']);	
	}
}
