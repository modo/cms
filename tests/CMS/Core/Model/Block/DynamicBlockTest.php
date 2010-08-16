<?php
namespace Core\Model\Block;

require_once 'PHPUnit/Framework.php';

/**
 * Test class for DynamicBlock.
 * Generated by PHPUnit on 2010-01-28 at 16:43:50.
 */
class DynamicBlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DynamicBlock
     */
    protected $block;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->block = new \Mock\Block\DynamicBlock(new \Core\Model\View('Core', 'test', 'test'));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testProcess()
    {
        $this->block->process();
    }

    public function testSetRequest()
    {
        $request = new \Zend_Controller_Request_Http();
        $this->block->setRequest($request);
        $this->assertEquals($request, $this->block->getRequest());
    }

    public function testSetEntityManager()
    {
        $em = new \Mock\EntityManager();
        $this->block->setEntityManager($em);
        $this->assertEquals($em, $this->block->getEntityManager());
    }
}
?>
