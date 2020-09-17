<?php

namespace Raketman\Bundle\ServiceVersioningBundle\DependencyInjection\Compiler;

use Raketman\Bundle\ServiceVersioningBundle\Exception\NotFoundVersionException;
use Raketman\Bundle\ServiceVersioningBundle\Services\Factory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class VersionFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // Найдем все версионные сервисы
        foreach ($container->findTaggedServiceIds("raketman.version.factory") as $serviceId => $attributes) {
            $service = $container->getDefinition($serviceId);

            // Создадим сервис, который потом добавим как фабрику к текущему
            $factoryName = 'raketman.version.factory_'.$serviceId;
            $factory = new Definition(Factory::class);
            $factory->setPublic(false);

            // Найдем все версии по тегу, который является названием сервиса
            $isFound = false;
            foreach ($container->findTaggedServiceIds($serviceId) as $id => $childAttributes) {
                foreach ($childAttributes as $attribute) {

                    // Получим версию из аттрибутом сервиса
                    $version = $attribute['version'];

                    // Добавим версию
                    $factory->addMethodCall("addVersion", array($version, new Reference($id)));

                    // укажем, что версии есть, иначе надо будет выкидывать Exception
                    $isFound = true;
                }
            }

            if (false === $isFound) {
                throw new \RuntimeException(sprintf(
                    'Can\'t compile informer handler by service id "%s".',
                    $serviceId
                ), 0, new NotFoundVersionException());
            }

            // Добавим resolver версий
            $factory->addMethodCall("setResolver", array(new Reference($attributes[0]['resolver'])));

            // Зарегаем сервис
            $container->setDefinition($factoryName, $factory);

            if (method_exists($service, 'setFactory')) {
                $service->setFactory([new Reference($factoryName), 'getClass']);
            } else {
                // установим его в качестве фабрики
                $service->setFactoryService(new Reference($factoryName));
                $service->setFactoryMethod( 'getClass');
            }


        }
    }
}
