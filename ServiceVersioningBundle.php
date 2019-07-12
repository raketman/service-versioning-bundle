<?php

namespace Raketman\Bundle\ServiceVersioningBundle;

use Raketman\Bundle\ServiceVersioningBundle\DependencyInjection\Compiler\VersionFactoryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ServiceVersioningBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new VersionFactoryPass());
    }
}
