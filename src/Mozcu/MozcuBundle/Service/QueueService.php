<?php

namespace Mozcu\MozcuBundle\Service;

use Doctrine\ORM\EntityManager;
use Mozcu\MozcuBundle\Entity\Album;
use Mozcu\MozcuBundle\Entity\AlbumUploadQueuePending;

class QueueService extends BaseService{
    
    public function __construct(EntityManager $entityManager) {
        parent::__construct($entityManager);
    }
    
    /**
     * 
     * @return string
     */
    public function toString() {
        return 'QueueService';
    }

    public function addNewAlbum(Album $album) {
        $queue = new \Mozcu\MozcuBundle\Entity\AlbumUploadQueuePending();
        $queue->setAlbum($album);
        $queue->setToUpdate(false);

        $this->getEntityManager()->persist($queue);
        $this->getEntityManager()->flush();
    }
    
    public function addUpdateAlbum(Album $album) {
        $queue = new AlbumUploadQueuePending();
        $queue->setAlbum($album);
        $queue->setToUpdate(true);
        
        $pending = $this->getEntityManager()->getRepository('MozcuMozcuBundle:AlbumUploadQueuePending')->findOneBy(array('album' => $album->getId()));
        $failed = $this->getEntityManager()->getRepository('MozcuMozcuBundle:AlbumUploadQueueFailed')->findOneBy(array('album' => $album->getId()));
        
        if(!is_null($pending)) {
            $this->getEntityManager()->remove($pending);
        }
        
        if(!is_null($failed)) {
            $this->getEntityManager()->remove($failed);
        }
        
        $this->getEntityManager()->persist($queue);
        $this->getEntityManager()->flush();
    }

}