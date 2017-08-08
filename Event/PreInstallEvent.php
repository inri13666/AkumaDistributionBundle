<?php

namespace Akuma\Bundle\DistributionBundle\Event;

use Akuma\Bundle\DistributionBundle\Model\InstallStepInterface;
use Symfony\Component\EventDispatcher\Event;

class PreInstallEvent extends Event
{
    const NAME = 'akuma.pre_install';

    /** @var InstallStepInterface[]|array */
    protected $steps;

    /**
     * @param InstallStepInterface[] $steps
     */
    public function __construct(array $steps)
    {
        $this->steps = $steps;
    }

    /**
     * @return InstallStepInterface[]|array
     */
    public function getSteps()
    {
        return $this->steps;
    }
}
