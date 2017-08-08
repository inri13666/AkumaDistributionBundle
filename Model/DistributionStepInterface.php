<?php

namespace Akuma\Bundle\DistributionBundle\Model;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface DistributionStepInterface
{
    /**
     * @return string
     */
    public function getStepName();

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output);
}
