<?php

namespace Mozcu\MozcuBundle\Lib;

class MemcachedConnector {
     
    /**
     *
     * @var \Memcached
     */
    private $memcached;
    
    public function __construct(array $memcachedData) {
        $this->memcached = new \Memcached(); 
        $this->memcached->addServer($memcachedData['host'], $memcachedData['port']);
    }
    
    /**
     * @return Memcached
     **/
    public function getMemcached() {
        return $this->memcached;
    }   
}