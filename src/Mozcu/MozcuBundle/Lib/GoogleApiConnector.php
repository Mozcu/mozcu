<?php

namespace Mozcu\MozcuBundle\Lib;

class GoogleApiConnector {
    
    private $clientId;
    
    private $serviceAccountName;
    
    private $keyFile;
    
    private $client;
    
    public function __construct(array $googleApiData) {
        $this->clientId = $googleApiData['client_id'];
        $this->serviceAccountName = $googleApiData['service_acount_name'];
        $this->keyFile = $googleApiData['key_file'];
        $this->app_name = $googleApiData['storage_app_name'];
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
