<?php

namespace Akuma\Bundle\DistributionBundle;

use Akuma\Bundle\DistributionBundle\Model\Bundle;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;

abstract class AkumaKernel extends Kernel
{
    const CACHE_FILE_NAME = 'bundles.map';
    const BUNDLE_IDENTIFIER = 'bundle.yml';

    /**
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances
     */
    public function registerBundles()
    {

        $cache = $this->getCacheDir() . DIRECTORY_SEPARATOR . static::CACHE_FILE_NAME;

        $bundles = [];

        if (is_readable($cache)) {
            $foundBundles = unserialize(file_get_contents($cache));
        } else {
            $foundBundles = $this->findBundles();
            if (!file_exists(dirname($cache))) {
                mkdir(dirname($cache), 0777, true);
            }
            file_put_contents($cache, serialize($foundBundles));
        }

        /** @var Bundle $bundle */
        foreach ($foundBundles as $bundle) {
            $bundles[] = $bundle->getInstance($this);
        }

        return $bundles;
    }

    /**
     * @return array
     */
    protected function findBundles()
    {
        $roots = array(
            implode(DIRECTORY_SEPARATOR, [$this->getRootDir(), '..', 'src']),
            implode(DIRECTORY_SEPARATOR, [$this->getRootDir(), '..', 'vendor']),
        );
        $bundles = [];
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
                function (\SplFileInfo $current) use (&$bundles) {
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
                            $parsed = Yaml::parse($file);
                            if (!isset($parsed['class'])) {
                                return false;
                            }

                            if (isset($parsed['environment'])) {
                                $envs = $parsed['environment'];
                                $envs = is_array($envs) ? $envs : [$envs];
                                if (!in_array($this->getEnvironment(), $envs)) {
                                    return false;
                                }
                            }

                            $bundleModel = new Bundle();
                            $bundles[] = $bundleModel->setClass($parsed['class'])
                                ->setPriority(isset($parsed['priority']) ? (int)$parsed['priority'] : 0)
                                ->setKernelRequired(isset($parsed['kernel']) ? (bool)$parsed['kernel'] : 0);
                        }

                        return false;
                    }
                }
            );

            $iterator = new \RecursiveIteratorIterator($filter);
            $iterator->rewind();
        }

        usort($bundles, function (Bundle $b1, Bundle $b2) {
            if ($b1->getPriority() > $b2->getPriority()) {
                return 1;
            }

            if ($b1->getPriority() < $b2->getPriority()) {
                return -1;
            }

            return 0;
        });

        return $bundles;
    }
}
