<?php

namespace Mozcu\MozcuBundle\Lib;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class GoogleApiConnector {
    
    /**
     *
     * @var Container
     */
    private $container;
    
    private $clientId;
    
    private $serviceAccountName;
    
    private $keyFile;
    
    private $client;
    
    public function __construct(Container $container) {
        $this->container = $container;
        
        $this->clientId = $this->container->getParameter('google_api.client_id');
        $this->serviceAccountName = $this->container->getParameter('google_api.service_acount_name');
        $this->keyFile = $this->container->getParameter('google_api.key_file');
        $this->app_name = $this->container->getParameter('google_api.storage_app_name');
    }
    
    public function connect($scope) {
        $this->client = new \Google_Client();
        $this->client->setApplicationName($this->app_name);
        
        $key = file_get_contents($this->keyFile);
        $this->client->setAssertionCredentials(new \Google_Auth_AssertionCredentials(
            $this->serviceAccountName,
            array($scope),
            $key)
        );
        $this->client->setScopes($scope);
        $this->client->setClientId($this->clientId);
    }
    
    /**
     * @return Google_Client
     **/
    public function getClient() {
        return $this->client;
    }
    
    
}
