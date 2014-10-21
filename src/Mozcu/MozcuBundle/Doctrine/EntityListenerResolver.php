<?php

namespace Mozcu\MozcuBundle\Doctrine;

use Doctrine\ORM\Mapping\DefaultEntityListenerResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Esta clase se encarga de devolver el listener adecuado para cada entidad
 */
class EntityListenerResolver extends DefaultEntityListenerResolver implements ContainerAwareInterface
{
    private $container;
    private $mapping;

     /**
     * Creates a container aware entity resolver.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container The container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
        $this->mapping = array();
    }

    /**
     * {@inheritdoc}
     */
    public function addMapping($className, $service)
    {
        $this->mapping[$className] = $service;
    }

    /**
     * Maps an entity listener to a service.
     *
     * @param string $className The entity listener class.
     * @param string $service   The service ID.
     */
    public function resolve($className)
    {
        if (isset($this->mapping[$className]) && $this->container->has($this->mapping[$className])) {
            return $this->container->get($this->mapping[$className]);
        }
        
        return parent::resolve($className);
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

}
