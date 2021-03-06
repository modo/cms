<?php

namespace Core\Model\Module;

/**
 * Represents a content type that is installed with the module
 *
 * @package     CMS
 * @subpackage  Core
 * @category    Model
 * @copyright   Copyright (c) 2009-2010 Modo Design Group (http://mododesigngroup.com)
 * @license     http://github.com/modo/cms/blob/master//LICENSE    New BSD License
 *
 * @Entity
 * @Table(name="module_content_type")
 * @property int $id
 */
class ContentType
    extends Resource
{
    /**
     * @var string
     * @Column(name="controller", type="string", length="100", nullable="true")
     */
    protected $controller;

    protected $resourceString = 'Content';

    public function __construct($title, $discriminator, $class, $controller = null)
    {
        parent::__construct($title, $discriminator, $class);
        $this->setController($controller);
    }

    public function setController($controller)
    {
        if(null !== $controller) {
            if (!class_exists($controller)) {
                throw new \Core\Model\Exception(sprintf('Class \'%s\' does not exist.', $controller));
            }
        }
        $this->controller = $controller;
        return $this;
    }
}