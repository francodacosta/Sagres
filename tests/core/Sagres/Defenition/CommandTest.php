<?php
namespace Sagres\Defenition;


require_once '/home/nuno/workspace/Sagres/core/Sagres/Defenition/Command.php';

/**
 * Test class for Command.
 * Generated by PHPUnit on 2012-03-02 at 17:24:14.
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Command
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Command;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Sagres\Defenition\Command::getName
     * @covers Sagres\Defenition\Command::setName
     */
    public function testSetGetName()
    {
        $this->object->setName('foo');
        $this->assertEquals('foo', $this->object->getName());
    }

    /**
     * @covers Sagres\Defenition\Command::getSummary
     * @covers Sagres\Defenition\Command::setSummary
     */
    public function testSetGetSummary()
    {
       $this->object->setSummary('foo');
       $this->assertEquals('foo', $this->object->getSummary());
    }

    /**
     * @covers Sagres\Defenition\Command::getExecutes
     * @todo Implement testGetExecutes().
     */
    public function testGetExecutes()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Sagres\Defenition\Command::addExecute
     * @todo Implement testAddExecute().
     */
    public function testAddExecute()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>
