<?php

namespace Asset\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Representation of a mime type
 *
 * @package     CMS
 * @subpackage  Asset
 * @category    Model
 * @copyright   Copyright (c) 2009-2010 Modo Design Group (http://mododesigngroup.com)
 * @license     http://github.com/modo/cms/blob/master//LICENSE    New BSD License
 *
 * @Entity
 * @Table(name="asset_mimetype")
 * @property string $sysname
 * @property Asset\Model\Type $type
 */
class MimeType extends \Core\Model\AbstractModel
{
    /**
     * @var integer
     * @Id @Column(name="sysname", type="string", length="50")
     */
    protected $sysname;

    /**
     * @var Asset\Model\Type
     * @ManyToOne(targetEntity="Asset\Model\Type", inversedBy="mimeTypes")
     * @JoinColumn(name="type", referencedColumnName="sysname", nullable="false")
     */
    protected $type;

    /**
     * @var Asset\Model\Extension
     * @OneToMany(targetEntity="Asset\Model\Extension", mappedBy="mimeType", cascade={"persist"})
     */
    protected $extensions;

    /**
     * @param string $sysname
     * @param Type $type
     */
    public function __construct($sysname, Type $type)
    {
        $this->setSysname($sysname);
        $this->setType($type);
        $this->setExtensions(new ArrayCollection());
    }

    /**
     * @param string $sysname
     * @return Type
     */
    public function setSysname($sysname)
    {
        $validator = new \Zend_Validate_StringLength(0, 50);
        if (!$validator->isValid($sysname)) {
            throw new \Core\Model\Exception('Sysname must be between 0 and 50 characters.');
        }
        $this->sysname = $sysname;
        return $this;
    }

    /**
     * @param Type $type
     * @return Extension
     */
    public function setType(Type $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param Extension $ext
     * @return MimeType
     */
    public function addExtension(Extension $ext)
    {
        $ext->setMimeType($this);
        $this->extensions[] = $ext;
        return $this;
    }

    /**
     * @param array $extensions
     * @return MimeType
     */
    public function addExtensions($extensions)
    {
        foreach ($extensions AS $ext) {
            $this->addExtension($ext);
        }
        return $this;
    }

    /**
     * @param string $ext
     * @return bool
     */
    public function isValidExtension($ext)
    {
        foreach ($this->getExtensions() AS $extension) {
            if ($extension->getSysname() == $ext) {
                return true;
            }
        }
        return false;
    }
}