<?php
namespace Core\Model\Block;

require_once 'PHPUnit/Framework.php';

/**
 * Test class for StaticBlock.
 * Generated by PHPUnit on 2010-01-28 at 16:43:55.
 */
class StaticBlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StaticBlock
     */
    protected $block;

    protected $content;
    protected $view;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->view = new \Mock\View();
        $this->content = new \Mock\Content();
        $this->block = new StaticBlock($this->content, $this->view);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testSetContent()
    {
        $this->assertEquals($this->content, $this->block->getContent());
    }

    public function testGetConfigValue()
    {
        $this->assertEquals('test', $this->block->getConfigValue('awesome'));
    }

    public function testContentIsAssignedToView()
    {
        $this->assertEquals($this->content, $this->block->getViewInstance()->content);
    }
}
?>
