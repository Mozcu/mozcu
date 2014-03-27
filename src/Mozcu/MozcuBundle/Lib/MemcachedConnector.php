<?php

namespace Mozcu\MozcuBundle\Lib;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class MemcachedConnector {
     
     /**
     *
     * @var Container
     */
    private $container;
    
    private $memcached;
    
    public function __construct(Container $container) {
        $this->container = $container;
        
        $host = $this->container->getParameter('memcached.host');
        $port = $this->container->getParameter('memcached.port');
        
        $this->memcached = new \Memcached(); 
        $this->memcached->addServer($host, $port); 
    }
    
    /**
     * @return Memcached
     **/
    public function getMemcached() {
        return $this->memcached;
    }   
}