<?php
namespace Mozcu\MozcuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineEntityListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $ems = $container->getParameter('doctrine.entity_managers');
        
        foreach ($ems as $name => $em) {
            $container->getDefinition(sprintf('doctrine.orm.%s_configuration', $name))
                ->addMethodCall('setEntityListenerResolver', [new Reference('mozcu_mozcu.doctrine.entity_listener_resolver')]);
        }
        
        $definition = $container->getDefinition('mozcu_mozcu.doctrine.entity_listener_resolver');
        $services = $container->findTaggedServiceIds('doctrine.entity_listener');
 
        foreach ($services as $service => $attributes) {
            $definition->addMethodCall(
                'addMapping',
                [$container->getDefinition($service)->getClass(), $service]
            );
        }
    }
}