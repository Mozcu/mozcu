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

    public function addAlbumToQueue(\Mozcu\MozcuBundle\Entity\Album $album, $update = false) {
        $queue = new \Mozcu\MozcuBundle\Entity\AlbumUploadQueuePending();
        $queue->setAlbum($album);
        
        if($update) {
            $pending = $this->getEntityManager()->getRepository('MozcuMozcuBundle:AlbumUploadQueuePending')->findOneBy(array('album' => $album->getId()));
            $failed = $this->getEntityManager()->getRepository('MozcuMozcuBundle:AlbumUploadQueueFailed')->findOneBy(array('album' => $album->getId()));
            
            $this->getEntityManager()->remove($pending);
            if(!is_null($failed)) {
                $this->getEntityManager()->remove($failed);
            }
            $this->getEntityManager()->flush();
        }
        $queue->setToUpdate($update);

        $this->getEntityManager()->persist($queue);
        $this->getEntityManager()->flush();
    }

}