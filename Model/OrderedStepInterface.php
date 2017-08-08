<?php

namespace Akuma\Bundle\DistributionBundle\Model;

interface OrderedStepInterface
{
    /**
     * @return int
     */
    public function getOrder();
}
