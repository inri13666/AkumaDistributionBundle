<?php

namespace Akuma\Bundle\DistributionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DistributionPass implements CompilerPassInterface
{
    const INSTALL_STEP_TAG = 'akuma.step.install';
    const UPDATE_STEP_TAG = 'akuma.step.update';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('akuma.distribution.registry');

        $taggedServices = array_keys($container->findTaggedServiceIds(self::INSTALL_STEP_TAG));

        foreach ($taggedServices as $id) {
            $definition->addMethodCall('addInstallStep', [new Reference($id)]);
        }

        $taggedServices = array_keys($container->findTaggedServiceIds(self::UPDATE_STEP_TAG));

        foreach ($taggedServices as $id) {
            $definition->addMethodCall('addUpdateStep', [new Reference($id)]);
        }
    }
}
