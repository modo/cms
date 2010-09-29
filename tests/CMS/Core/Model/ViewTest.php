<?php
namespace Core\Model;

require_once 'PHPUnit/Framework.php';

/**
 * Test class for View.
 * Generated by PHPUnit on 2009-12-28 at 15:28:19.
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var View
     */
    protected $view;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->view = new View('Core', 'test', 'test');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testMapViewScriptCore()
    {
        $this->view->module = 'Core';
        $this->view->type   = 'Text';
        $this->view->sysname = 'Text';

        $this->assertEquals(APPLICATION_PATH.'/Core/View/', $this->view->getBasePath());
        $this->assertEquals('Text/text.phtml', $this->view->getFile());
    }

    public function testMapViewScriptModule()
    {
        $this->view->module = 'Blog';
        $this->view->type   = 'Article';
        $this->view->sysname = 'Article';

        $this->assertEquals(APPLICATION_PATH.'/Blog/View/', $this->view->getBasePath());
        $this->assertEquals('Article/article.phtml', $this->view->getFile());
    }

    public function testConstructor()
    {
        $view = new View('Core', 'Text', 'Text');
        $this->assertEquals('Core', $view->module);
        $this->assertEquals('Text', $view->type);
        $this->assertEquals('Text', $view->sysname);
    }

    public function testRender()
    {
        $this->view->module = 'Core';
        $this->view->type   = 'Text';
        $this->view->sysname = 'default';
        $this->view->getInstance()->render($this->view->getFile());

        $this->view->getInstance()->render('Text/default.phtml');
    }

    public function testGetInstance()
    {
        $helperLoader = new \Zend_Loader_PluginLoader();
        \Core\Model\View::setPluginLoader($helperLoader, 'helper');

        $filterLoader = new \Zend_Loader_PluginLoader();
        \Core\Model\View::setPluginLoader($filterLoader, 'filter');
    }
}
?>
