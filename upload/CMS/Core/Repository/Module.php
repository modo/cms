<?php

namespace Core\Repository;

/**
 * Repository for the module model
 *
 * @package     CMS
 * @subpackage  Core
 * @category    Repository
 * @copyright   Copyright (c) 2009-2010 Modo Design Group (http://mododesigngroup.com)
 * @license     <license>
 */
class Module extends \Doctrine\ORM\EntityRepository
{
    public function findAll()
    {
        $qb = \Zend_Registry::get('doctrine')->getRepository('Core\Model\Module')->createQueryBuilder('m');
        $qb->select('m, b, c')
           ->leftJoin('m.blockTypes', 'b')
           ->leftJoin('m.contentTypes', 'c');

        return $qb->getQuery()->getResult();
    }
}