<?php

namespace Mozcu\MozcuBundle\Lib;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Mozcu\MozcuBundle\Exception\GoogleStorageException;

class GoogleStorageService {
    
    /**
     *
     * @var Container
     */
    private $container;
    
    private $googleApi;
    
    private $service;
    
    private $bucket;
    
    public function __construct(Container $container, $googleApi) {
        $this->container = $container;
        $this->googleApi = $googleApi;
        $this->bucket = $this->container->getParameter('google_api.storage_bucket');
        
        $scope = $this->container->getParameter('google_api.storage_scope');
        $this->googleApi->connect($scope);
        $this->service = new \Google_Service_Storage($this->googleApi->getClient());

        
    }
    
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
    
    
}
