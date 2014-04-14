<?php

namespace Mozcu\MozcuBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Mozcu\MozcuBundle\Exception\AppException;
use Mozcu\MozcuBundle\Lib\GoogleStorageService;

class FileService extends BaseService{
    
    const SONG_DIR_PREFIX = 's';
    const IMAGE_DIR_PREFIX = 'i';
    const UPLOAD_DIR = '/var/www/mozcu/web/static';
    
    /**
     *
     * @var Container
     */
    private $container;
    
    private $uploadDir;
    
    /**
     *
     * @var GoogleStorageService
     */
    private $googleStorage;
    
    public function __construct(EntityManager $entityManager, Container $container, GoogleStorageService $googleStorage) {
        parent::__construct($entityManager);
        $this->container = $container;
        $this->uploadDir = self::UPLOAD_DIR;
        $this->googleStorage = $googleStorage; 
    }
    
    /**
     * 
     * @return string
     */
    public function toString() {
        return 'FileService';
    }
    
    public function processSong(array $songData) {
        try {
            $dir = self::SONG_DIR_PREFIX . md5($songData['album_id']);
            
            $ext = pathinfo($songData['song_path'], PATHINFO_EXTENSION);
            if(empty($ext)) {
                $ext = 'mp3';
            }
            $name = $songData['name'] . '.' . $ext;
            
            $mimeType = $this->getMimeType($songData['song_path']);
            
            if(isset($songData['track_number'])) {
                $name = $songData['track_number'] . ' - ' . $name;
            }
    
            $response = $this->googleStorage->upload($songData['song_path'], $name, $mimeType);
            
            return json_encode(array('success' => true, 'song_url' => $response->mediaLink, 'song_directory' => $dir));
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function processImage($imageData) {
        try {
            $dir = self::IMAGE_DIR_PREFIX . md5(serialize($imageData));
            
            $presResponse = array();
            $presentations = unserialize($imageData['presentations']);
            foreach($presentations as $key => $pres) {
                $image = new \Imagick($imageData['image_path']);
                if(isset($pres['thumbnail']) && $pres['thumbnail']) {
                    $image->cropthumbnailimage($pres['width'], $pres['height']);
                } else {
                    $image->resizeImage($pres['width'], $pres['height'], null, 0.9, true);
                }
                
                if(isset($pres['customDir']) && !empty($pres['customDir'])) {
                    $originalDir = $dir;
                    $dir = $pres['customDir'];
                    $realPath = $this->uploadDir . '/' . $dir;
                    if(!is_dir($realPath)) {
                        mkdir($realPath);
                    }
                }
                
                $ext = pathinfo($imageData['name'], PATHINFO_EXTENSION);
                $date = new \DateTime;
                $name = md5($imageData['name'] . $key . $date->getTimestamp());
                $name = $name . '.' . $ext;
                
                $image->writeimage($realPath . '/' . $name);
                $pres['url'] = '/static/' . $dir . '/' . $name;
                $presResponse[] = $pres;
                
                if(isset($originalDir)) {
                    $dir = $originalDir;
                    $realPath = $this->uploadDir . '/' . $dir;
                    unset($originalDir);
                }
            }
            
            return json_encode(array('success' => true, 'presentations' => $presResponse));
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function getMimeType($filePath) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        return $mime;
    }
}
