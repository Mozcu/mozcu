<?php
namespace Mozcu\MozcuBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Mozcu\MozcuBundle\Service\QueueService;
use Mozcu\MozcuBundle\Entity\Album;

class AlbumListener
{
    /**
     *
     * @var QueueService
     */
    private $queueService;
    
    public function __construct(QueueService $queueService) {
        $this->queueService = $queueService;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $event
     */
    public function postPersist(Album $album, LifecycleEventArgs $event) {
        $this->queueService->addNewAlbum($album);
    }
}
