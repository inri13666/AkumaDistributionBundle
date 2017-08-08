<?php

namespace Akuma\Bundle\DistributionBundle;

use Akuma\Bundle\DistributionBundle\DependencyInjection\Compiler\DistributionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AkumaDistributionBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DistributionPass());
    }
}
