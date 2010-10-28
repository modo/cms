<?php

namespace Core\Service\Mediator;

/**
 * @package     CMS
 * @subpackage  Core
 * @category    Model
 * @copyright   Copyright (c) 2009-2010 Modo Design Group (http://mododesigngroup.com)
 * @license     http://github.com/modo/cms/blob/master//LICENSE    New BSD License
 */
class ContentMediator extends \Core\Service\AbstractMediator
{

    /**
     * @var \Taxonomy\Service\Term
     */
    protected $_termService;

    /**
     * @var \User\Service\User
     */
    protected $_userService;

    public function init()
    {
        $self = $this;
        $this->setFields(
            array(
                'id' => array(
                    'setMethod' => false
                ),
                'author' => array(
                    'getMethod' => function ($instance) {
                        return $instance->getAuthor() ? 
                                $instance->getAuthor()->getId()
                                : null;
                    },
                    'setMethod' => function ($instance, $value) use ($self) {
                        if (null !== $value) {
                            $value = $self->getUserService()->getUser($value);
                        }
                        $instance->setAuthor($value);
                    }
                ),
                'authorName' => array(
                ),
                'creationDate' => array(
                    'getMethod' => function ($instance) {
                        return $instance->getCreationDate()->format('m-d-Y');
                    },
                    'filterMethod' => function ($instance, $value) {
                        try {
                            return $value ? \date_create_from_format('m-d-Y', $value) : \DateTime();
                        } catch (\Exception $e) {
                            throw new \Exception(sprintf('Invalid date passed: %s', $value));
                        }
                    }
                ),
                'modificationDate' => array(
                    'getMethod' => function ($instance) {
                        return $instance->getModificationDate()->format('m-d-Y');
                    },
                    'filterMethod' => function ($instance, $value) {
                        try {
                            return $value ? \date_create_from_format('m-d-Y', $value) : \DateTime();
                        } catch (\Exception $e) {
                            throw new \Exception(sprintf('Invalid date passed: %s', $value));
                        }
                    }
                ),
                'tags' => array(
                    'getMethod' => function ($instance) {
                        $tags = array();
                        foreach ($instance->getTags() AS $tag) {
                            $tags[] = $tag->getName();
                        }
                        return $tags;
                    },
                    'setMethod' => function ($instance, $values) use ($self) {
                        $tags = array();
                        foreach ($values AS $tagName) {
                            $tags[] = $self->getTermService()->getOrCreateTerm($tagName, 'contentTags');
                        }
                        $instance->setTags($tags);
                    }
                )
            )
        );
    }

    /**
     * @param \User\Service\User $userService
     */
    public function setUserService(\User\Service\User $userService)
    {
        $this->_userService = $userService;
    }

    /**
     * @return User\Service\User
     */
    public function getUserService()
    {
        return $this->_userService;
    }

    /**
     * @param \Taxonomy\Service\Term $termService
     */
    public function setTermService(\Taxonomy\Service\Term $termService)
    {
        $this->_termService = $termService;
    }

    /**
     * @return \Taxonomy\Service\Term
     */
    public function getTermService()
    {
        return $this->_termService;
    }
}