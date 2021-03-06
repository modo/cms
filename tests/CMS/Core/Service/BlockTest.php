<?php
namespace Core\Service;

require_once 'PHPUnit/Framework.php';
//require_once '../../../bootstrap.php';

use \Mockery as m;

/**
 * Test class for AbstractService.
 */
class BlockTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function tearDown()
    {
        m::close();
    }

    public function testGetVariables()
    {
        $data = array('title' => 'metadata', 'content' => 'metadata');

        $meta = m::mock();
        $meta->shouldReceive('getReflectionProperties')->andReturn($data);

        $em = m::mock(new \Mock\EntityManager());

        // test with inputs
        $propertyOne = 'one';
        $propertyTwo = 'two';

        $view = new \Mock\View();
        $block = new \Mock\Block\DynamicBlock($view);
        $block->addConfigProperties(array(
            new \Core\Model\Block\Config\Property($propertyOne, 'value'),
            new \Core\Model\Block\Config\Property($propertyTwo, 'value')
        ));
        
        $blockService = new \Core\Service\Block($em);
        $vars = $blockService->getVariables($block);

        $this->assertTrue(in_array($propertyOne, $vars));
        $this->assertTrue(in_array($propertyTwo, $vars));

        // test another case
        $type = 'Core\Model\Content\Text';
        $content = new \Core\Model\Content\Placeholder('sysname', $type, 'text placeholder');
        $staticBlock = new \Core\Model\Block\StaticBlock($content, $view);
        $em->shouldReceive('getClassMetadata')->with($type)->andReturn($meta);

        $vars = $blockService->getVariables($staticBlock);

        $this->assertEquals($vars, array_keys($data));
        
        // test another case
        $content = new \Core\Model\Content\Text('title', 'content', false);
        $staticBlock = new \Core\Model\Block\StaticBlock($content, $view);
        $em->shouldReceive('getClassMetadata')->with(\get_class($content))->andReturn($meta);

        $vars = $blockService->getVariables($staticBlock);
    }

    public function testRemoveConfigDependencies()
    {
        $block = m::mock('Core\Model\Block');

        $inheritBlock = m::mock('Core\Model\Block');
        $value1 = new \Core\Model\Block\Config\Value('name', 'value', $inheritBlock);

        $entity = m::mock();
        $entity->shouldReceive('getDependentValues')->with($block)->andReturn(array($value1));

        $em = m::mock(new \Mock\EntityManager());
        $em->shouldReceive('getRepository')->andReturn($entity);

        $blockService = new \Core\Service\Block($em);

        $this->assertEquals($inheritBlock, $value1->getInheritsFrom());

        $blockService->removeConfigDependencies($block);

        $this->assertEquals(null, $value1->getInheritsFrom());
    }

    public function testDispatchBlockAction()
    {
        $sc = new \stdClass();

        $em = m::mock(new \Mock\EntityManager());
        $block = m::mock('Core\Model\Block');
        $action = 'actionName';
        $request = m::mock('Zend_Controller_Request_Http');
        $controller = m::mock(new MockBlockController);
        $controller->shouldReceive('setServiceContainer')->with($sc);
        $controller->shouldReceive('setRequest')->with($request);

        $blockService = m::mock(new \Core\Service\Block($em), array(m::BLOCKS => array('dispatchBlockAction')));
        $blockService->shouldReceive('getBlockControllerObject')->andReturn($controller);
        $blockService->shouldReceive('getServiceContainer')->andReturn($sc);

        $blockService->dispatchBlockAction($block, $action, $request);

        $this->setExpectedException('Exception');
        $blockService->dispatchBlockAction($block, 'thisActionDoesNotExist', $request);
    }
    
    public function testGetBlockControllerObject()
    {
        $em = m::mock(new \Mock\EntityManager());
        $block = m::mock('Core\Model\Block');

        $blockService = m::mock(new \Core\Service\Block($em), array(m::BLOCKS => array('getBlockControllerObject')));
        $blockService->shouldReceive('getBlockController')->with($block)->andReturn('stdClass');

        $this->assertEquals(new \stdClass(), $blockService->getBlockControllerObject($block));

        $this->setExpectedException('Exception');
        $blockService = m::mock(new \Core\Service\Block($em), array(m::BLOCKS => array('getBlockControllerObject')));
        $blockService->shouldReceive('getBlockController')->with($block)->andReturn(null);
        $blockService->getBlockControllerObject($block);
    }

    public function testGetBlockController()
    {
        $em = m::mock(new \Mock\EntityManager());

        $module = new \Core\Model\Module('sysname', 'title');
        $module->addResource(new \Core\Model\Module\ContentType('title', 'discriminator', 'Core\Service\MockContent', 'Core\Service\MockContentController'));

        $storage = m::mock();
        $storage->shouldReceive('getModules')->andReturn(array($module));
        
        $registry = m::mock('Core\Module\Registry');
        $registry->shouldReceive('getDatabaseStorage')->andReturn($storage);

        $content = new MockContent();
        $view = new \Mock\View();
        $block = new \Core\Model\Block\StaticBlock($content, $view);

        $blockService = new \Core\Service\Block($em);
        $blockService->setModuleRegistry($registry);

        $this->assertEquals('Core\Service\MockContentController', $blockService->getBlockController($block));
    }
    
    public function testDelete()
    {
        $block = m::mock('Core\Model\Block\StaticBlock');
        
        $em = m::mock('Doctrine\ORM\EntityManager');
        $em->shouldReceive('remove')->with($block)->ordered();
        $em->shouldReceive('flush')->ordered();

        $staticBlockService = m::mock();
        $staticBlockService->shouldReceive('delete')->with($block);

        $blockService = m::mock(new \Core\Service\Block($em), array(m::BLOCKS => array('delete')));
        $blockService->shouldReceive('removeConfigDependencies')->with($block);
        $blockService->shouldReceive('getStaticBlockService')->once()->andReturn($staticBlockService);

        $blockService->delete($block);
    }
    
    public function testInitBlock()
    {
        $em = m::mock('Doctrine\ORM\EntityManager');
        $request = m::mock('Zend_Controller_Request_Http');
        $sc = m::mock('sfServiceContainer');

        $block = m::mock('Core\Model\Block\DynamicBlock');
        $block->shouldReceive('setRequest')->with($request);
        $block->shouldReceive('setServiceContainer')->with($sc);
        $block->shouldReceive('init');
        $block->shouldReceive('run');

        $blockService = m::mock(new \Core\Service\Block($em), array(m::BLOCKS => array('initBlock')));
        $blockService->shouldReceive('getServiceContainer')->andReturn($sc);
        $blockService->initBlock($block, $request);

        $block = m::mock('Core\Model\Block\StaticBlock');
        $block->shouldReceive('init')->never();
        $blockService->initBlock($block, $request);
    }
    
    public function testCanView()
    {
        $em = m::mock('Doctrine\ORM\EntityManager');
        $auth = m::mock('Zend_Auth');
        $auth->shouldReceive('getIdentity');

        $block = m::mock('Core\Model\Block');
        $block->shouldReceive('canView');

        $blockService = new \Core\Service\Block($em);
        $blockService->setAuth($auth);

        $blockService->canView($block);
    }

    public function testUpdateLocation()
    {
        $em = m::mock('Doctrine\ORM\EntityManager');

        $view = new \Mock\View();
        $content = m::mock('Core\Model\Content');
        $block = new \Core\Model\Block\StaticBlock($content, $view);

        $location = new \Core\Model\Layout\Location('left');

        $locationService = m::mock();
        $locationService->shouldReceive('getLocation')->andReturn($location);

        $blockService = m::mock(new \Core\Service\Block($em), array(m::BLOCKS => array('updateLocation')));
        $blockService->shouldReceive('getLocationService')->andReturn($locationService);

        $newBlock = $blockService->updateLocation($block, 'left');
        $this->assertEquals($location, $block->location);
    }

    public function testUpdateWeight()
    {
        $em = m::mock('Doctrine\ORM\EntityManager');

        $view = new \Mock\View();
        $content = m::mock('Core\Model\Content');
        $block = new \Core\Model\Block\StaticBlock($content, $view);

        $blockService = new \Core\Service\Block($em);

        $newBlock = $blockService->updateWeight($block, 2);
        $this->assertEquals(2, $block->weight);

        $this->setExpectedException('Exception');
        $blockService->updateWeight($block, 'one');
    }

    public function testUpdate()
    {
        $em = m::mock('Doctrine\ORM\EntityManager');

        $block = m::mock('Core\Model\Block');

        $blockService = m::mock(new \Core\Service\Block($em), array(m::BLOCKS => array('update')));
        $blockService->shouldReceive('updateLocation')->with($block, 'main')->once();
        $blockService->shouldReceive('updateWeight')->with($block, 1)->once();

        $b1 = new \stdClass();
        $b1->id = 1;
        $b1->location = 'main';
        $b1->weight = 1;

        $blockService->update($block, $b1);
    }
}

class MockBlockController
{
    public function actionName()
    {
        
    }
}

class MockContent extends \Core\Model\Content {}

class MockContentController {}