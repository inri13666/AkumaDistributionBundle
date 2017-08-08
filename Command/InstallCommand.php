<?php

namespace Akuma\Bundle\DistributionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends ContainerAwareCommand
{
    const NAME = 'akuma:install';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName(self::NAME)->ignoreValidationErrors();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $processor = $this->getContainer()->get('akuma.distribution.step_processor');
        $processor->install($input, $output);
    }
}
