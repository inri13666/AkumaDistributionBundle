<?php

namespace Akuma\Bundle\DistributionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class DebugBundleCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('akuma:debug:bundle')
            ->setDescription('List registered bundles');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->getApplication()->getKernel();
        $table = new Table($output);
        $table->setHeaders([])->setRows([]);
        $bundles = $kernel->getBundles();
        foreach ($bundles as $key => $bundle) {
            $table->addRow([
                $key,
                get_class($bundle),
            ]);
        }
        $table->render();
    }

}
