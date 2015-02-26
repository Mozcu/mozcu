<?php

namespace Mozcu\MozcuBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use \Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Mozcu\MozcuBundle\Exception\AppException;
use Mozcu\MozcuBundle\Exception\ServiceException;
use Mozcu\MozcuBundle\Lib\GoogleStorageService;
use Mozcu\MozcuBundle\Entity\Album;

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
    
    /**
     * 
     * @param string $filePath
     * @return string
     */
    private function getMimeType($filePath) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        
        return $mime;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @throws AppException
     */
    public function deleteAlbumFromStaticServer(Album $album) {
        try {
            //Songs
            foreach($album->getSongs() as $song) {
                $this->google_storage->delete($song->getStaticFileName());
            }
            //Images
            foreach($album->getImage()->getPresentations() as $pres) {
                $this->google_storage->delete($pres->getStaticFileName());
            }
            //Zip
            if(!is_null($album->getStaticZipFileName())) {
                $this->google_storage->delete($album->getStaticZipFileName());
            }
            //$this->google_storage->delete($album->getStaticDirectory());
        } catch (\Exception $e) {
            throw new AppException("Error al eliminar archivos de un album: {$e->getMessage()}");
        }
    }
    
    /**
     * TODO: optimizar bajando y agregando al zip directamente
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @return array
     * @throws ServiceException
     */
    public function generateZip(Album $album) {
        $tmpDir = $this->container->getParameter('uploads.tmp_zip_dir');
        $baseDir = $tmpDir . '/' . $album->getId();
        $filesDir = $baseDir . '/files';
        
        // creando los directorios temporales del album
        mkdir($baseDir);
        mkdir($filesDir);
        
        $files = $this->downloadAlbumFilesForZip($album, $filesDir);
        
        $zip = new \ZipArchive();
        $zipName = $album->getReleaseDate() . ' - ' . $this->sanitizeString($album->getName());
        $zipPath = $baseDir . '/' .$zipName . '.zip';
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            throw new ServiceException('Error al crear el archivo Zip');
        }
        
        foreach ($files as $file) {
            $zip->addFile($file['path'], $file['name']);
        }
        $zip->close();

        foreach ($files as $file) {
            unlink($file['path']);
        }
        rmdir($filesDir);
        $staticZipName = $album->getStaticDirectory() . '/' . $zipName;
        $response = $this->google_storage->upload($zipPath, $staticZipName, $this->getMimeType($zipPath));
        unlink($zipPath);
        rmdir($baseDir);
        
        return $response;
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @param string $filesDir
     * @return array
     */
    private function downloadAlbumFilesForZip(Album $album, $filesDir) {
        $staticDir = $album->getStaticDirectory();
        $response = array();
        
        foreach($album->getSongs() as $song) {
            $fileObject = $this->google_storage->get($song->getStaticFileName());
            $file = file_get_contents($fileObject->getMediaLink());
            $trackNumber = sprintf("%02s", $song->getTrackNumber());
            $fileName = $trackNumber . ' - ' . str_replace($staticDir .'/', '', $fileObject->getName());
            $filePath = $filesDir . '/' . $fileName;
            file_put_contents($filePath, $file);
            $response[] = array('path' => $filePath, 'name' => $fileName);
        }
        
        $fileObject = $this->google_storage->get($album->getCoverImagePresentation()->getStaticFileName());
        $file = file_get_contents($fileObject->getMediaLink());
        $coverImageName = str_replace($staticDir .'/', '', $fileObject->getName());
        $filePath = $filesDir . '/' . $coverImageName;
        file_put_contents($filePath, $file);
        $response[] = array('path' => $filePath, 'name' => $coverImageName);
        
        return $response;
    }
    
    /**
     * 
     * @param string $string
     * @return string
     */
    protected function sanitizeString($string) {
        // Remove anything which isn't a word, whitespace, number
        // or any of the following caracters -_~,;:[]().
        $string = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $string);
        // Remove any runs of periods
        $string = preg_replace("([\.]{2,})", '', $string);
        
        return $string;
    }
    
}
