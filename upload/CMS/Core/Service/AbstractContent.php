<?php

namespace Core\Service;

/**
 * Abstract service for any service that serves a Core\Model\Content class
 *
 * @package     CMS
 * @subpackage  Core
 * @category    Service
 * @copyright   Copyright (c) 2009-2010 Modo Design Group (http://mododesigngroup.com)
 * @license     http://github.com/modo/cms/blob/master//LICENSE    New BSD License
 */
abstract class AbstractContent extends AbstractModel
{
    protected $_termService;
    protected $_userService;

    protected function _create($data)
    {
        $content = parent::_create($data);
        return $this->_setContentObjects($content, $data);
    }

    protected function _update($data)
    {
        $content = parent::_update($data);
        return $this->_setContentObjects($content, $data, false);
    }

    protected function _setContentObjects(\Core\Model\Content $content, array $data, $throwErrors = true)
    {
        if (isset($data['author']['id']) && $data['author']['id'] != '') {
            $author = $this->getUserService()->getUser($data['author']['id']);
            $content->setAuthor($author);
            $content->setAuthorName($author->getFirstName() . ' ' . $author->getLastName());
        } elseif (isset($data['authorName']) && '' != $data['authorName']) {
            $content->setAuthorName($data['authorName']);
        } elseif ($throwErrors) {
            throw \Core\Exception\ValidationException::invalidData(\get_class($content), array('author' => array('required' => 'a valid author id or author name is required')));
        }

        if(isset($data['tags'])) {
            $tags = array();
            if(\is_array($data['tags'])) {
                $tags = $this->getTermService()->getOrCreateTerms($data['tags'], 'contentTags');
            }
            $content->setTags($tags);
        }

        return $content;
    }

    public function setTermService(\Taxonomy\Service\Term $termService)
    {
        $this->_termService = $termService;
    }

    protected function getTermService()
    {
        return $this->_termService;
    }

    public function setUserService(\User\Service\User $userSerivce)
    {
        $this->_userService = $userSerivce;
    }

    protected function getUserService()
    {
        return $this->_userService;
    }
}