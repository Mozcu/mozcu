<?php

namespace Mozcu\MozcuBundle\Service;

use Mozcu\MozcuBundle\Exception\ServiceException;

class ActionLogService
{
    private $url;
    
    private $token;
    
    /**
     *
     * @var \GuzzleHttp\Client 
     */
    private $client;
    
    public function __construct(array $options)
    {
        $this->url = $options['url'];
        $this->token = $options['token'];
        $this->client = new \GuzzleHttp\Client(['base_uri' => $this->url]);
    }
    
    /**
     * 
     * @param array $data
     * @return boolean
     * @throws ServiceException
     */
    public function logAlbum(array $data)
    {
        $response = $this->doPost('albums', ['album' => $data]);
        if ($response['success']) {
            return true;
        }
        throw new ServiceException();
    }
    
    /**
     * 
     * @param array $data
     * @return boolean
     * @throws ServiceException
     */
    public function logSong(array $data)
    {
        $response = $this->doPost('songs', ['song' => $data]);
        if ($response['success']) {
            return true;
        }
        throw new ServiceException();
    }
    
    /**
     * 
     * @param array $data
     * @return boolean
     * @throws ServiceException
     */
    public function logDownload(array $data)
    {
        $response = $this->doPost('downloads', ['download' => $data]);
        if ($response['success']) {
            return true;
        }
        throw new ServiceException();
    }
    
    /**
     * 
     * @param string $resource
     * @param array $params
     * @return array
     * @throws ServiceException
     */
    private function doPost($resource, $params) {
        $params['access_token'] = $this->token;
        $data = ['form_params' => $params];
        
        $response = $this->client->request('POST', $resource, $data);
        if ($response->getStatusCode() != 200) {
            throw new ServiceException();
        }
        
        return json_decode($response->getBody(), true);
    }
}
