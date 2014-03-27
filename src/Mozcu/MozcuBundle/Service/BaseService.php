<?php

namespace Mozcu\MozcuBundle\Service;

use \Doctrine\ORM\EntityManager;


Abstract class BaseService {
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    
    public function __construct(EntityManager $entityManager) {
        $this->em = $entityManager;
    }
    
    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager() {
        return $this->em;
    }
}