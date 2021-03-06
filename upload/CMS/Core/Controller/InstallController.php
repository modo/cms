<?php

namespace Core\Controller;

/**
 * Installs the core module and all default modules
 *
 * @package     CMS
 * @subpackage  Core
 * @category    Controller
 * @copyright   Copyright (c) 2009-2010 Modo Design Group (http://mododesigngroup.com)
 * @license     http://github.com/modo/cms/blob/master//LICENSE    New BSD License
 */
class InstallController extends AbstractInstallController
{
    protected $moduleName = 'Core';

    protected $classes = array(

        'Core\Model\AbstractActivity',
        'Core\Model\Activity\ContentActivity',
        'Core\Model\Activity\PageActivity',

        'Core\Model\Module',
        'Core\Model\Module\Resource',
        'Core\Model\Module\ActivityType',
        'Core\Model\Module\BlockType',
        'Core\Model\Module\ContentType',
        'Core\Model\Module\View',

        'Core\Model\AbstractPage',
        'Core\Model\Page',
        'Core\Model\Template',
        'Core\Model\Layout',
        'Core\Model\Layout\Location',
        'Core\Model\PageRoute',
        'Core\Model\Route',
        'Core\Model\Block',
        'Core\Model\Block\Config\Value',
        'Core\Model\Content',
        'Core\Model\Address',

        'Core\Model\Content\Placeholder',
        'Core\Model\Content\Text',

        // Core now depends on some models in User
        'User\Model\User',
        'User\Model\Identity',
        'User\Model\Identity\Local',
        'User\Model\Identity\OpenID',
        'User\Model\Session',
        'User\Model\Group',

        'User\Model\User\FailedLogin',

        'User\Model\Acl\Role',
        'User\Model\Acl\Permission',
        'User\Model\Acl\Privilege',
        'User\Model\Acl\Resource',
        'User\Model\Acl\RoleAssignment\AbstractRoleAssignment',
        'User\Model\Acl\RoleAssignment\UserRoleAssignment',
        'User\Model\Acl\RoleAssignment\GroupRoleAssignment',

        // Core now depends on some models in Taxonomy
        'Taxonomy\Model\Vocabulary',
        'Taxonomy\Model\Term',
    );

    public function installAction ()
    {
        $this->clearCacheAction();

        echo '<h3>Installing Core</h3>';
        echo '<b>Creating tables...</b><br/>';
        ob_flush();
        $this->createSchema();
        echo '<b>Tables created.</b><br/><br/>';

        echo '<b>Registering Module...</b><br/>';
        ob_flush();
        $this->registerModule();
        echo '<b>Module registered.</b><br/><br>';

        echo '<b>Creating Base Models...</b><br/>';
        ob_flush();
        $this->_createBase();
        echo '<b>Base models created.</b><br/></br>';

        echo 'Creating module routes...<br/>';
        ob_flush();
        $this->_createRoutes();

        echo '<b>Creating 404 Page...</b><br/>';
        ob_flush();
        $this->_create404();
        echo '<b>404 page created.</b><br/></br>';

        echo '<b>Creating 403 Page...</b><br/>';
        ob_flush();
        $this->_create403();
        echo '<b>403 page created.</b><br/></br>';

        echo 'Creating Shared Text Index Page...<br/>';
        ob_flush();
        $this->_createSharedTextIndexPage();

        echo 'Creating Edit Shared Text Page...<br/>';
        ob_flush();
        $this->_createSharedTextEditPage();

        echo '<h3>Core Module Installed</h3>';
        ob_flush();

        // Install default modules
        $this->_helper->actionStack('install', 'install', 'blog');
        $this->_helper->actionStack('install', 'install', 'asset');
        $this->_helper->actionStack('install', 'install', 'taxonomy');
        $this->_helper->actionStack('install', 'install', 'user');
    }

    public function _createBase ()
    {
        $homepage = new \Core\Model\Route('');
        $homepage->isDirect = false;
        $homepage->sysname = 'home';
        $this->_em->persist($homepage);

        $moduleDirect = new \Core\Model\Route('direct/:module/:controller/:action');
        $moduleDirect->sysname = 'DirectModule';
        $moduleDirect->isDirect = true;
        $this->_em->persist($moduleDirect);

        $coreDirect = new \Core\Model\Route('direct/:controller/:action');
        $coreDirect->sysname = 'DirectCore';
        $coreDirect->isDirect = true;
        $this->_em->persist($coreDirect);

        $sitemap = new \Core\Model\Route('sitemap.xml', 'sitemap', array(
            'module' => 'default',
            'controller' => 'page',
            'action' => 'sitemap'
        ));
        $sitemap->isDirect = true;
        $this->_em->persist($sitemap);

        echo 'Creating locations<br/>';
        $left = new \Core\Model\Layout\Location('left');
        $right = new \Core\Model\Layout\Location('right');
        $main = new \Core\Model\Layout\Location('main');
        $span = new \Core\Model\Layout\Location('span');

        echo 'Creating layouts<br/>';
        $layout1 = new \Core\Model\Layout('default');
        $layout1->setTitle('3 Columns');
        $layout1->setLocations(array($left, $right, $main));
        $this->_em->persist($layout1);

        $layout2 = new \Core\Model\Layout('2col');
        $layout2->setTitle('2 Columns (right sidebar)');
        $layout2->setLocations(array($right, $main));
        $this->_em->persist($layout2);

        $layout3 = new \Core\Model\Layout('2colalt');
        $layout3->setTitle('2 Columns (left sidebar)');
        $layout3->setLocations(array($left, $main));
        $this->_em->persist($layout3);

        $layout4 = new \Core\Model\Layout('1col');
        $layout4->setTitle('1 Column');
        $layout4->setLocations(array($main));
        $this->_em->persist($layout4);

        $layout = new \Core\Model\Layout('home');
        $layout->setTitle('Home');
        $layout->setLocations(array($span, $right, $main));
        $this->_em->persist($layout);

        $layout5 = new \Core\Model\Layout('findapro');
        $layout5->setTitle('Find A Pro');
        $layout5->setLocations(array($span, $left, $main));
        $this->_em->persist($layout5);

        $this->_em->flush();
    }

    public function _createHomepage()
    {
        $page = new \Core\Model\SystemPage($this->_em->getReference('Core\Model\Layout', 'home'));
        $homeRoute = $this->_em->getRepository('Core\Model\Route')->getRoute('home');
        $this->_em->persist($homeRoute->routeTo($page));

        $this->_em->persist($page);
        $this->_em->flush();
    }

    public function _createSharedTextIndexPage()
    {
        $page = new \Core\Model\SystemPage($this->_em->getReference('Core\Model\Layout', 'default'));
        $this->_em->persist($page);

        $view = $this->_sc->getService('moduleService')->getBlockView('Core', 'textIndex', 'default');
        $block = new \Core\Block\TextIndex($view);
        $page->addBlock($block, 'main');

        $this->_sc->getService('pageRouteService')->createAndRouteTo($page, 'index', array('type' => 'text'));

        $this->_em->flush();
    }

    public function _createSharedTextEditPage()
    {
        $page = new \Core\Model\SystemPage($this->_em->getReference('Core\Model\Layout', 'default'));
        $this->_em->persist($page);

        $view = $this->_sc->getService('moduleService')->getBlockView('Core', 'textEdit', 'default');
        $block = new \Core\Block\Form\Text($view);
        $page->addBlock($block, 'main');

        $route = new \Core\Model\Route('edit/text/*', 'edit-text');
        $this->_em->persist($route);
        $this->_em->flush();

        $this->_sc->getService('pageRouteService')->createAndRouteTo($page, 'edit-text');

        $this->_em->flush();
    }

    public function _create404()
    {
        $layout = $this->_em->getRepository('Core\Model\Layout')->findOneBySysname('default');
        $main = $this->_em->getRepository('Core\Model\Layout\Location')->findOneBySysname('main');
        $page = new \Core\Model\SystemPage($layout);
        $this->_em->persist($page);

        $route = new \Core\Model\Route('404', '404');
        $this->_em->persist($route);
        $this->_em->persist($route->routeTo($page));

        $searchUrl = '/search';
        $block404Text = <<<EOD
<div class="error404">
<h1>Error 404 - OOPS! It looks like you've requested a page that is no longer available.</h1>
</div><!--error404-->
EOD;
        $view = $this->getModule()->getContentType('Text')->getView('default');
        $text = new \Core\Model\Content\Text('404 Not Found', $block404Text, true);
        $block = new \Core\Model\Block\StaticBlock($text, $view);
        $page->addBlock($block, 'main');

        $this->_em->persist($text);

        $this->_em->flush();
    }

    public function _create403()
    {
        $layout = $this->_em->getRepository('Core\Model\Layout')->findOneBySysname('default');
        $main = $this->_em->getRepository('Core\Model\Layout\Location')->findOneBySysname('main');
        $page = new \Core\Model\SystemPage($layout);
        $this->_em->persist($page);

        $route = new \Core\Model\Route('403', '403');
        $this->_em->persist($route);
        $this->_em->persist($route->routeTo($page));

        $block403Text = <<<EOD
<h1>Sorry, you do not have access to this page.</h1>
EOD;
        $view = $this->getModule()->getContentType('Text')->getView('default');
        $text = new \Core\Model\Content\Text('403 Permission Denied', $block403Text, true);
        $block = new \Core\Model\Block\StaticBlock($text, $view);
        $page->addBlock($block, 'main');

        $this->_em->persist($text);

        $this->_em->flush();
    }

    public function clearCacheAction()
    {
        $memcacheImpl = \Zend_Registry::get('doctrine')->getConfiguration()->getMetadataCacheImpl();
        if($memcacheImpl instanceof \Doctrine\Common\Cache\MemcacheCache) {
            $memcacheImpl->getMemcache()->flush();
        } else if ($memcacheImpl instanceof \Doctrine\Common\Cache\ApcCache) {
            apc_clear_cache('user');
        }
    }

    public function _createRoutes()
    {
        $route = new \Core\Model\Route('index/:type', 'index');
        $this->_em->persist($route);
    }
}