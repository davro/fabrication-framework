<?php

namespace Fabrication\Tests;

use Fabrication\Fabrication;
use Fabrication\DataEntity;

class DataEntityTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->engine = Fabrication::createInstance('engine');
        $this->entity = DataEntity::create();
    }

    public function testInstance() {

        $this->assertInternalType('object', $this->entity);
        $this->assertInstanceOf('Fabrication\DataEntity', $this->entity);
    }

    public function testSetName() {

        $this->assertTrue($this->entity->setName('testing set'));
    }

    public function testSetNameEmpty() {

        $this->assertNull($this->entity->setName(''));
    }

    public function testGetName() {

        $this->assertTrue($this->entity->setName('testing get'));
        $this->assertEquals('testing get', $this->entity->getName());
    }

    public function testSetNamespace() {

        $this->assertTrue($this->entity->setNamespace('testing'));
    }

    public function testSetNamespaceEmpty() {

        $this->assertNull($this->entity->setNamespace(''));
    }

}
