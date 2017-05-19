<?php

namespace Akuma\Bundle\DistributionBundle;

use Akuma\Bundle\DistributionBundle\Configuration\BundleConfiguration;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;

abstract class AkumaKernel extends Kernel
{
    const CACHE_FILE_NAME = 'bundles.map';
    const BUNDLE_IDENTIFIER = 'bundle.yml';

    /**
     * Returns an array of bundles to register.
     *
     * @param array $bundles
     *
     * @return \Symfony\Component\HttpKernel\Bundle\BundleInterface[] An array of bundle instances
     */
    public function registerBundles(array $bundles = [])
    {

        $cache = $this->getCacheDir() . DIRECTORY_SEPARATOR . static::CACHE_FILE_NAME;

        $bundleClasses = array_map(function ($item) {
            return get_class($item);
        }, $bundles);

        $bundles = array_combine(array_values($bundleClasses), array_values($bundles));

        if (is_readable($cache)) {
            $foundBundleResources = unserialize(file_get_contents($cache));
        } else {
            $roots = array(
                implode(DIRECTORY_SEPARATOR, [$this->getRootDir(), '..', 'src']),
                implode(DIRECTORY_SEPARATOR, [$this->getRootDir(), '..', 'vendor']),
            );

            $foundBundleResources = $this->findBundleResources($roots);
            if (!file_exists(dirname($cache))) {
                mkdir(dirname($cache), 0777, true);
            }
            file_put_contents($cache, serialize($foundBundleResources));
        }

        $foundBundles = $this->findBundleConfigurations($foundBundleResources);

        $require = [];
        foreach ($foundBundles as $config) {
            $bundleClass = $config[BundleConfiguration::NODE_CLASS];
            if(!array_key_exists($bundleClass, $bundles)){
                $bundles[$bundleClass] = $this->createInstance($config);
            }
            $require = array_merge($require, $config[BundleConfiguration::NODE_REQUIRE]);
        }

        foreach ($require as $config){
            $bundleClass = $config[BundleConfiguration::NODE_CLASS];
            if(!array_key_exists($bundleClass, $bundles)){
                $bundles[$bundleClass] = $this->createInstance($config);
            }
        }

        return $bundles;
    }

    /**
     * @param array $config
     *
     * @return object
     */
    protected function createInstance(array $config)
    {
        $bundleClass = $config[BundleConfiguration::NODE_CLASS];

        if ($config[BundleConfiguration::NODE_KERNEL]) {
            return new $bundleClass($this);
        }

        return new $bundleClass;
    }

    /**
     * @param array $config
     * @param array $requireBundles
     *
     * @return array
     */
    protected function buildRequire(array $config, array $requireBundles = [])
    {
        $bundles = [];

        foreach ($config[BundleConfiguration::NODE_REQUIRE] as $config) {
            $bundleClass = trim($config[BundleConfiguration::NODE_CLASS], '\\');
            if (!array_key_exists($bundleClass, $requireBundles)) {
                $bundles[$bundleClass] = $this->createInstance($config);
            }
        }

        return $bundles;
    }

    /**
     * @param array $roots
     *
     * @return array
     */
    protected function findBundleResources(array $roots)
    {
        $resources = [];

        foreach ($roots as $root) {
            $root = realpath($root);

            if (!$root || !is_dir($root) || !is_readable($root)) {
                continue;
            }

            $dir = new \RecursiveDirectoryIterator(
                $root,
                \FilesystemIterator::FOLLOW_SYMLINKS | \FilesystemIterator::SKIP_DOTS
            );

            $filter = new \RecursiveCallbackFilterIterator(
                $dir,
                function (\SplFileInfo $current) use (&$resources) {
                    if (!$current->getRealPath()) {
                        return false;
                    }
                    $fileName = strtolower($current->getFilename());
                    if ($fileName === 'tests' || $current->isFile()) {
                        return false;
                    }
                    if (!is_dir($current->getPathname() . '/Resources')) {
                        return true;
                    } else {
                        $file = $current->getPathname() . '/Resources/config/' . static::BUNDLE_IDENTIFIER;

                        if (is_file($file) && is_readable($file)) {

                            $resources[] = $file;
                        }

                        return false;
                    }
                }
            );

            $iterator = new \RecursiveIteratorIterator($filter);
            $iterator->rewind();
        }

        return $resources;
    }

    /**
     * @param array $bundleResources
     *
     * @return array
     *
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    protected function findBundleConfigurations(array $bundleResources = [])
    {
        $bundleConfigs = [];

        foreach ($bundleResources as $file) {
            $processor = new Processor();
            $config = $processor->processConfiguration(
                new BundleConfiguration(),
                Yaml::parse(file_get_contents($file))
            );

            $envs = $config[BundleConfiguration::NODE_ENVIRONMENTS];

            if (!empty($envs) && !in_array($this->getEnvironment(), $envs)) {
                continue;
            }

            $bundleConfigs[$config[BundleConfiguration::NODE_CLASS]] = $config;
        }

        usort($bundleConfigs, function (array $config1, array $config2) {
            $b1 = $config1[BundleConfiguration::NODE_PRIORITY];
            $b2 = $config2[BundleConfiguration::NODE_PRIORITY];

            if ($b1 > $b2) {
                return 1;
            }

            if ($b1 < $b2) {
                return -1;
            }

            return 0;
        });

        return $bundleConfigs;
    }
}
