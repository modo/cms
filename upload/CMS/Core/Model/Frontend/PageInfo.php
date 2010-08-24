<?php

namespace Core\Model\Frontend;

/**
 * Returns information on a page
 *
 * @package     CMS
 * @subpackage  Core
 * @category    Model
 * @copyright   Copyright (c) 2009-2010 Modo Design Group (http://mododesigngroup.com)
 * @license     <license>
 */
class PageInfo extends \Core\Model\Frontend
{

    protected $basePath = '/resources/core/js/modo/build/block/';
    
    public function success(\Core\Model\AbstractPage $page)
    {
        parent::__construct();
        $frontendPage = new \stdClass();
        $frontendPage->id = $page->id;
        $frontendPage->locations = array();

        $frontendPage->actions = $this->_getPageActions($page);
        foreach ($page->layout->locations AS $location) {
            $frontendLocation = new \stdClass();
            $frontendLocation->sysname = $location->sysname;
            $frontendLocation->blocks = array();
            $frontendPage->locations[$location->sysname] = $frontendLocation;
        }
        foreach($page->getBlocks() AS $block) {
            if ($block->canView(\Core\Auth\Auth::getInstance()->getIdentity())) {
                if (isset($frontendPage->locations[$block->location->sysname])) {
                    $frontendBlock = new \stdClass();
                    $frontendBlock->id = $block->id;
                    $frontendBlock->properties = \Core\Service\Manager::get('Core\Service\Block')->getVariables($block);
                    $frontendPage->locations[$block->location->sysname]->blocks[] = $frontendBlock;
                    $frontendBlock->actions = $this->_getBlockActions($block);
                }
            }
        }
        $this->data[] = $frontendPage;

        return $this;
    }

    public function _getBlockActions(\Core\Model\Block $block)
    {
        $actions = array();
        if ($block->canMove(\Core\Auth\Auth::getInstance()->getIdentity())) {
            $move = new Action('block-move');
            $move->source = $this->basePath . 'block-move.js';
            $move->plugin = 'BlockMove';
            $actions[$move->name] = $move;
        }
        if ($block->canEdit(\Core\Auth\Auth::getInstance()->getIdentity())) {
            $edit = new Action('block-edit', '/direct/block/edit/?id=' . $block->id);
            $edit->source = $this->basePath . 'block-edit.js';
            $edit->plugin = 'BlockEdit';
            $actions[$edit->name] = $edit;
        }
        if ($block->canConfigure(\Core\Auth\Auth::getInstance()->getIdentity())) {
            $configure = new Action('block-configure', '/direct/block/configure/?id=' . $block->id);
            $configure->source = $this->basePath . 'block-configure.js';
            $configure->plugin = 'BlockConfigure';
            $actions[$configure->name] = $configure;
        }
        if ($block->canDelete(\Core\Auth\Auth::getInstance()->getIdentity())) {
            $delete = new Action('block-delete', '/direct/block/delete/?id=' . $block->id);
            $delete->source = $this->basePath . 'block-delete.js';
            $delete->plugin = 'BlockDelete';
            $actions[$delete->name] = $delete;
        }
        return $actions;
    }

    public function _getPageActions($page)
    {
        $actions = array();

        if ($page->canEdit(\Core\Auth\Auth::getInstance()->getIdentity())) {
            $rearrange = new Action('blockRearrange', '/direct/page/rearrange?id=' . $page->id);
            $actions[$rearrange->name] = $rearrange;
        }

        if ($page->canEdit(\Core\Auth\Auth::getInstance()->getIdentity())) {
            $add = new Action('addBlock', '/direct/page/add-block?id=' . $page->id);
            $add->source = $this->basePath . 'block-add.js';
            $add->plugin = 'BlockAdd';
            $actions[$add->name] = $add;
        }

        return $actions;
    }
}