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
        $params = $this->em->getConnection()->getParams();
        $config = new \Doctrine\DBAL\Configuration();
        $conn = \Doctrine\DBAL\DriverManager::getConnection($params, $config);
        
        $ids = $conn->fetchAll("SELECT id FROM song WHERE album_id = {$album->getId()}");
        $conn->close();
        
        $oldSongIds = array_map(function($row) {
            return $row['id'];
        }, $ids);
        
        $newSongIds = array_map(function($song) {
            return $song->getId();
        }, $album->getSongs()->toArray());
        sort($newSongIds, SORT_NUMERIC);
        
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
