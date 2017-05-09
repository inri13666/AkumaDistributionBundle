<?php

namespace Akuma\Bundle\DistributionBundle\Command;

use Akuma\Bundle\DistributionBundle\AkumaKernel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class RebuildBundlesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('akuma:distribution:rebuild')
            ->setDescription('Rebuilds bundles cache');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->getApplication()->getKernel();
        $cache = $kernel->getCacheDir() . DIRECTORY_SEPARATOR . AkumaKernel::CACHE_FILE_NAME;
        unlink($cache);
        if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $output->writeln(sprintf('Unlinked cache file "%s"', $cache));
        }
    }

}
