<?php

namespace Mozcu\MozcuBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Mozcu\MozcuBundle\Exception\AppException;
use Mozcu\MozcuBundle\Lib\GoogleStorageService;

class UploadService extends BaseService{
    
    /**
     *
     * @var Container
     */
    private $container;
    
    /**
     *
     * @var GoogleStorageService
     */
    private $google_storage;
    
    public function __construct(EntityManager $entityManager, Container $container) {
        parent::__construct($entityManager);
        $this->container = $container;
        $this->google_storage = $this->container->get('mozcu_mozcu.google_storage');
    }
    
    /**
     * 
     * @return string
     */
    public function toString() {
        return 'UploadService';
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return string
     * @throws \Mozcu\MozcuBundle\Service\Exception
     */
    public function processTemporarySong(UploadedFile $file) {
        try {
            $validTypes = $this->container->getParameter('uploads.valid_song_types');
            
            if(!in_array($file->getMimeType(), $validTypes)) {
                throw new AppException('Invalid file type');
            }
            
            /* Copying Song */
            $tmpDir = $this->container->getParameter('uploads.tmp_songs_dir');
            $date = new \DateTime();
            $timestamp = $date->getTimestamp();
            
            $newSongName = $timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($tmpDir, $newSongName);
            
            return $newSongName;
        } catch(\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * 
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return string
     * @throws \Mozcu\MozcuBundle\Service\Exception
     */
    public function processTemporaryImage(UploadedFile $file) {
        try {
            $validTypes = $this->container->getParameter('uploads.valid_image_types');
            
            if(!in_array($file->getMimeType(), $validTypes)) {
                throw new AppException('Invalid file type');
            }
            
            /* Copying Image */
            $tmpDir = $this->container->getParameter('uploads.tmp_images_dir');
            
            $date = new \DateTime();
            $timestamp = $date->getTimestamp();
            
            $newImageName = $timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($tmpDir, $newImageName);
            
            return $newImageName;
        } catch(\Exception $e) {
            throw new AppException($e->getMessage());
        }
    }

    /**
     * 
     * @param string $temporaryImageName
     * @param array $presentationsData
     * @return array
     * @throws AppException
     */
    public function uploadImageToStaticServer($temporaryImageName, array $presentationsData) {
        $tmpDir = $this->container->getParameter('uploads.tmp_images_dir');
        $tmpPath = $tmpDir . '/' . $temporaryImageName;
        
        $date = new \DateTime;
        $dir = $date->getTimestamp() . uniqid();
        if(!is_dir($tmpDir . '/' . $dir)) {
            mkdir($tmpDir . '/' . $dir);
        }
        
        $presResponse = array();
        foreach($presentationsData as $pres) {
            $imagick = new \Imagick($tmpPath);
            if(isset($pres['thumbnail']) && $pres['thumbnail']) {
                $imagick->cropthumbnailimage($pres['width'], $pres['height']);
            } else {
                $imagick->resizeImage($pres['width'], $pres['height'], null, 0.9, true);
            }

            $ext = pathinfo($tmpPath, PATHINFO_EXTENSION);
            $name = $pres['name'] . '.' . $ext;
            $path = $tmpDir . '/' . $dir . '/' . $name;

            $imagick->writeimage($path);
            
            try {
                $response = $this->google_storage->upload($path, $dir . '/' . $name, $this->getMimeType($path));
                //var_dump($response->id); die;
            } catch (\Mozcu\MozcuBundle\Exception\GoogleStorageException $e) {
                unlink($path);
                throw new AppException('Error al intentar subir una imagen');
            }
            
            $pres['url'] = $response->mediaLink;
            $presResponse[] = $pres;
            unlink($path);
        }
        
        rmdir($tmpDir . '/' . $dir);
        unlink($tmpPath);
        
        return $presResponse;
    }
    
    private function getMimeType($filePath) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        return $mime;
    }
    
}
