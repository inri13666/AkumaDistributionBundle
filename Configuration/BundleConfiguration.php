<?php

namespace Akuma\Bundle\DistributionBundle\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class BundleConfiguration implements ConfigurationInterface
{
    const NODE_ENVIRONMENTS = 'environments';
    const NODE_KERNEL = 'kernel';
    const NODE_CLASS = 'class';
    const NODE_PRIORITY = 'priority';
    const NODE_REQUIRE = 'require';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bundle');
        $rootNode
            ->children()
                ->scalarNode(self::NODE_CLASS)
                    ->info('The name of bundle\'s class')
                    ->example('Acme\Bundle\AcmeIncBundle')
                    ->isRequired()
                    ->beforeNormalization()->always(function ($v) {
                        return trim($v, '\\');
                    })->end()
                    ->validate()
                        ->ifTrue(function ($v) { return !class_exists($v); })
                        ->thenInvalid('Class "%s" must be valid')
                    ->end()
                ->end()
                ->integerNode(self::NODE_PRIORITY)
                    ->info('Bundle\'s priority')
                    ->defaultValue(0)
                ->end()
                ->booleanNode(self::NODE_KERNEL)
                    ->info('Indicates if bundle loader require Kernel')
                    ->defaultFalse()
                ->end()
                ->arrayNode(self::NODE_ENVIRONMENTS)
                    ->beforeNormalization()->ifString()->then(function ($v) { return [$v]; })->end()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode(self::NODE_REQUIRE)
                    //->addDefaultsIfNotSet()
                    ->info('List of bundles that required for current bundle')
                    ->prototype('array')
                        ->children()
                            ->scalarNode(self::NODE_CLASS)
                                ->isRequired()
                                ->beforeNormalization()->always(function ($v) {
                                    return trim($v, '\\');
                                })->end()
                                ->validate()
                                    ->ifTrue(function ($v) { return !class_exists($v); })
                                    ->thenInvalid('Class %s must be valid')
                                ->end()
                            ->end()
                            ->booleanNode(self::NODE_KERNEL)->defaultFalse()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;


        return $treeBuilder;

    }
}
