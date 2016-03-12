<?php

namespace Mozcu\MozcuBundle\Lib;

use Mozcu\MozcuBundle\Exception\GoogleStorageException;

class GoogleStorageService {
    
    private $googleApi;
    
    private $service;
    
    private $bucket;
    
    public function __construct($googleApi, array $googleApiData) {
        $this->googleApi = $googleApi;
        $this->bucket = $googleApiData['storage_bucket'];
        $this->googleApi->connect($googleApiData['storage_scope']);
        $this->service = new \Google_Service_Storage($this->googleApi->getClient());

        
    }
    
    /**
     * 
     * @param string $filePath
     * @param string $name
     * @param string $mimeType
     * @return array
     * @throws GoogleStorageException
     */
    public function upload($filePath, $name, $mimeType) {
        $objects = $this->service->objects;
        $gso = new \Google_Service_Storage_StorageObject();
        
        $gso->setName($name);
        $fileData = file_get_contents($filePath);
        $postbody = array('data' => $fileData, 'uploadType' => 'multipart', 'mimeType' => $mimeType);
        try {
            return $objects->insert($this->bucket, $gso, $postbody);
        } catch(Exception $e) {
            throw new GoogleStorageException($e->getMessage(), $e->getCode());
        }
    }
    
    /**
     * 
     * @param string $name
     * @return string
     * @throws GoogleStorageException
     */
    public function delete($name) {
        try {
            return $this->service->objects->delete($this->bucket,$name);
        } catch(Exception $e) {
            throw new GoogleStorageException($e->getMessage(), $e->getCode());
        }   
    }
    
    /**
     * 
     * @param string $name
     * @return mixed
     * @throws GoogleStorageException
     */
    public function get($name) {
        try {
            return $this->service->objects->get($this->bucket,$name);
        } catch (Exception $e) {
            throw new GoogleStorageException($e->getMessage(), $e->getCode());
        }
    }
    
    
}
