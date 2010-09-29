<?php

namespace Core;

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Core\AllTests::main');
}

require_once __DIR__ . '/../../bootstrap.php';

class AllTests
{
    public static function main()
    {
        \PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new \CMSTestSuite('Core Tests');

        $suite->addTestSuite('Core\Controller\Request\HttpTest');
        $suite->addTestSuite('Core\Controller\Router\RewriteTest');

        $suite->addTestSuite('Core\Model\Frontend\ActionTest');
        $suite->addTestSuite('Core\Model\Frontend\CodeTest');
        $suite->addTestSuite('Core\Model\Frontend\PageInfoTest');
        $suite->addTestSuite('Core\Model\Frontend\SimpleTest');

        $suite->addTestSuite('Core\Model\AbstractModelTest');
        $suite->addTestSuite('Core\Model\AbstractPageTest');
        $suite->addTestSuite('Core\Model\BlockTest');
        $suite->addTestSuite('Core\Model\ContentTest');
        $suite->addTestSuite('Core\Model\LayoutTest');
        $suite->addTestSuite('Core\Model\PageRouteTest');
        $suite->addTestSuite('Core\Model\PageTest');
        $suite->addTestSuite('Core\Model\RouteTest');
        $suite->addTestSuite('Core\Model\TemplateTest');
        $suite->addTestSuite('Core\Model\ModuleTest');
        $suite->addTestSuite('Core\Model\FrontendTest');

        $suite->addTestSuite('Core\Model\Block\DynamicBlockTest');
        $suite->addTestSuite('Core\Model\Block\StaticBlockTest');
        $suite->addTestSuite('Core\Model\Block\Config\PropertyTest');
        $suite->addTestSuite('Core\Model\Block\Config\ValueTest');
        $suite->addTestSuite('Core\Model\Block\Config\Property\TextTest');
        $suite->addTestSuite('Core\Model\Block\Dynamic\Form\AbstractFormTest');

        $suite->addTestSuite('Core\Model\Content\PlaceholderTest');
        $suite->addTestSuite('Core\Model\Content\TextTest');

        $suite->addTestSuite('Core\Model\Module\BlockTypeTest');
        $suite->addTestSuite('Core\Model\Module\ContentTypeTest');
        $suite->addTestSuite('Core\Model\Module\ViewTest');

        $suite->addTestSuite('Core\Model\Layout\LocationTest');

        return $suite;
    }
}