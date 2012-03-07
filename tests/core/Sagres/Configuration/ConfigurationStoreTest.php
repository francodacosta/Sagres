<?php
namespace Sagres\Configuration;

class ConfigurationStoreTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $properties = array(
            'section 1' => array('a' => 'a','b' => 'b'),
            'section 2' => 'b',
            'section 3' => 'c',
        );
        $this->object = new ConfigurationStore($properties);
    }


    /**
     * @covers Sagres\Configuration\ConfigurationStore::__construct
     */
    public function testContructor()
    {
        $props = array(1,2,3);
        $o = new ConfigurationStore($props);

        $this->assertSame($props, $o->getData());
    }

    /**
    * @covers Sagres\Configuration\ConfigurationStore::setData
    * @covers Sagres\Configuration\ConfigurationStore::mergeArrays
    * @covers Sagres\Configuration\ConfigurationStore::getData
    */
    public function testSetGetData()
    {
        $props = array(1,2,3);
        $o =  new ConfigurationStore(null);
        $o->setData($props);
        $this->assertSame($props, $o->getData());
    }

    /**
    * @covers Sagres\Configuration\ConfigurationStore::setData
    * @covers Sagres\Configuration\ConfigurationStore::getData
    */
    public function testSetGetData_merge()
    {
        $props = array(1,2,3);
        $o =  new ConfigurationStore(array(2,3,4));
        $o->setData($props);
        $this->assertSame(array(1,2,3), $o->getData());
    }
    /**
    * @covers Sagres\Configuration\ConfigurationStore::setData
    * @covers Sagres\Configuration\ConfigurationStore::getData
    */
    public function testSetGetData_merge_deeper()
    {
        $props = array('section 1' => array('a' => '0'));
        $o =  $this->object;
        $o->setData($props);

        $result = array(
                'section 1' => array('a'=>'0', 'b' => 'b'),
                'section 2' => 'b',
                'section 3' => 'c',
        );
        $this->assertSame($result, $o->getData());
    }

    /**
     * @covers Sagres\Configuration\ConfigurationStore::getSection
     */
    public function testGetSection()
    {
        $o =  $this->object;
        $result = 'b';
        $this->assertSame($result, $o->getSection('section 2'));
    }

    /**
     * @covers Sagres\Configuration\ConfigurationStore::getSection
     */
    public function testHasSection()
    {
        $o =  $this->object;
        $this->assertTrue($o->hasSection('section 1'));
        $this->assertTrue($o->hasSection('section 2'));
        $this->assertTrue($o->hasSection('section 3'));

        $this->assertFalse($o->hasSection('section 3xczxczc'));
    }


    public function testSetSection()
    {
        $o =  $this->object;
        $o->setSection('section 1', array(1));
        $this->assertEquals(array(1), $o->getSection('section 1'));
    }
}