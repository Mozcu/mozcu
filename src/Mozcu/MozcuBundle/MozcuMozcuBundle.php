<?php

namespace Mozcu\MozcuBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Mozcu\MozcuBundle\DependencyInjection\Compiler\DoctrineEntityListenerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MozcuMozcuBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineEntityListenerPass());
    }
}
