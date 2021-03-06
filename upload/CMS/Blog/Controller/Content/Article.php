<?php

namespace Blog\Controller\Content;

/**
 * Controller for the blog article content type
 *
 * @package     CMS
 * @subpackage  Asset
 * @category    Controller
 * @copyright   Copyright (c) 2009-2010 Modo Design Group (http://mododesigngroup.com)
 * @license     http://github.com/modo/cms/blob/master//LICENSE    New BSD License
 */
class Article extends \Core\Controller\Content\AbstractController
{
    public function addAction()
    {
        throw new \Exception('Not implemented yet.');
        
        $view = \Core\Module\Registry::getInstance()->getModule('Core')->getBlockType('Form')->getView('default');
        $block = new \Blog\Block\Form\Article($view);

        $block->init();
        
        return $block->render();
    }

    public function editAction(\Core\Model\Block $block)
    {
        $frontend = new \Core\Model\Frontend\Simple();
        
        $blogService = \Core\Service\Manager::get('Blog\Service\Blog');
        $data = $this->getRequest()->getPost();

        // @var $form \Zend_Form
        $form = $blogService->getEditForm($block->content, $data);
        $form->setAction('/direct/block/edit/?id='.$block->id);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($data)) {
                // update the article
                $form->removeElement('id');
                $block->content->setData($form->getValues());
                $this->getEntityManager()->flush();
                $frontend->html = $block->render();
            } else {
                $frontend->html = $form->render();
                $frontend->fail();
            }
        } else {
            $frontend->html = $form->render();
        }
        return $frontend;
    }

    public function deleteAction(\Core\Model\Block $block)
    {
        //throw new \Exception('Not implemented yet.');
    }
}