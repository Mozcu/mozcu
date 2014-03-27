<?php

namespace Mozcu\MozcuBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Mozcu\MozcuBundle\Exception\AppException;

class QueueService extends BaseService{
    
    /**
     *
     * @var Container
     */
    private $container;
    
    public function __construct(EntityManager $entityManager, Container $container) {
        parent::__construct($entityManager);
        $this->container = $container;
    }
    
    /**
     * 
     * @return string
     */
    public function toString() {
        return 'QueueService';
    }

    public function addAlbumToQueue(\Mozcu\MozcuBundle\Entity\Album $album) {
        $queue = new \Mozcu\MozcuBundle\Entity\AlbumUploadQueuePending();
        $queue->setAlbum($album);

        $this->getEntityManager()->persist($queue);
        $this->getEntityManager()->flush();
    }

}