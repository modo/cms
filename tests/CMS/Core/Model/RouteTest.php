<?php
namespace Core\Model;

require_once 'PHPUnit/Framework.php';

/**
 * Test class for Route.
 * Generated by PHPUnit on 2009-12-28 at 16:14:10.
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Route
     */
    protected $route;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->route = new Route('');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testConstructor()
    {
        $route = new Route('test');
        $this->assertEquals('test', $route->getTemplate());
    }

    public function testMatchDirect()
    {
        $this->route = new Route('direct/:module/:controller/:action');
        $this->route->id = 21;
        $this->route->isDirect = true;

        $r = $this->route->match('/direct/module/controller/action');
        $this->assertEquals(21, $r['routeId']);
    }

    public function testMatchIndirect()
    {
        $this->route = new Route('test');
        $this->route->id = 14;
        $this->route->isDirect = false;

        $r = $this->route->match('/test');
        $this->assertEquals(14, $r['routeId']);
    }

    public function testRouteTo()
    {
        $this->route = new Route('');
        $page = new \Core\Model\Page(new \Core\Model\Layout('test'));
        $pageRoute = $this->route->routeTo($page);

        $params = $pageRoute->getParams();
        $this->assertTrue(empty($params));
        $this->assertEquals($page, $pageRoute->getPage());
    }

    public function testSetSysname()
    {
        $this->route->setSysname('newSysname');
        $this->assertEquals('newSysname', $this->route->getSysname());

        $this->route->setSysname(null);
        $this->assertNull($this->route->getSysname());

        $this->setExpectedException('Core\Model\Exception');
        // 101 characters
        $this->route->setSysname('27226405402722640540272264054027226405402722640540272264054027226405402722640540272264054027226405401');
    }

    public function testSetPageRoutes()
    {
        $this->setExpectedException('Core\Model\Exception');
        $this->route->setPageRoutes(1);
    }

    public function testSetIsDirect()
    {
        $this->route->setIsDirect(true);
        $this->assertTrue($this->route->getIsDirect());

        $this->route->setIsDirect(false);
        $this->assertFalse($this->route->getIsDirect());

        $this->setExpectedException('Core\Model\Exception');
        $this->route->setIsDirect('bananas');
    }

    public function testGetIdentifier()
    {
        $this->assertEquals(null, $this->route->getIdentifier());
    }

    public function testSetData()
    {
        $this->route->setData(array(
           'sysname' => 'testName'
        ));
        $this->assertEquals('testName', $this->route->getSysname());
    }

    public function testIsSet()
    {
        $this->assertEquals(null, $this->route->getId());
        $this->assertEquals(FALSE, isset($this->route->id));

        $this->route->setId(8);
        $this->assertEquals(TRUE, isset($this->route->id));
    }

    public function testSetTemplate()
    {
        $this->setExpectedException('Exception');
        $this->route->setTemplate('test');
    }

    public function testGet()
    {
        $this->route->version;
    }

    public function testCall()
    {
        $this->setExpectedException('Exception');
        $this->route->getDoesNotExist();
    }
}
?>
