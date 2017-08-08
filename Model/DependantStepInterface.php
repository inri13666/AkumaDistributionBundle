<?php

namespace Akuma\Bundle\DistributionBundle\Model;

interface DependantStepInterface
{
    /**
     * @return array
     */
    public function getDependencies();
}
