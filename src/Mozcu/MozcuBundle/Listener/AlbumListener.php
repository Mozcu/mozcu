<?php
namespace Mozcu\MozcuBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Mozcu\MozcuBundle\Service\QueueService;
use Doctrine\ORM\EntityManager;
use Mozcu\MozcuBundle\Entity\Album;

class AlbumListener
{
    /**
     *
     * @var QueueService
     */
    private $queueService;
    
    /**
     *
     * @var EntityManager
     */
    private $em;
    
    private $addToUpdateQueue;
    
    public function __construct(EntityManager $em, QueueService $queueService) {
        $this->queueService = $queueService;
        $this->em = $em;
        $this->addToUpdateQueue = false;
    }
    
    public function preUpdate(Album $album, PreUpdateEventArgs $event) {
        $oldSongIds = $this->em->getRepository('MozcuMozcuBundle:Song')->getSongIdsFromAlbum($album);
        
        $newSongIds = array_map(function($song) {
            return $song->getId();
        }, $album->getSongs()->toArray());
        sort($newSongIds, SORT_NUMERIC);
        
        var_dump($newSongIds);
        var_dump($oldSongIds);
        
        
        if($oldSongIds != $newSongIds) {
            $album->setIsActive(false);
            $this->addToUpdateQueue = true;
        }
        
        if(!is_null($album->getImage()->getTemporalFileName())) {
            $album->setIsActive(false);
            $this->addToUpdateQueue = true;
        }
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $event
     */
    public function postPersist(Album $album, LifecycleEventArgs $event) {
        $this->queueService->addNewAlbum($album);
    }
    
    public function postUpdate(Album $album, LifecycleEventArgs $event) {
        if($this->addToUpdateQueue) {
            $this->queueService->addUpdateAlbum($album);
        }
        $this->addToUpdateQueue = false;
    }
    
}
