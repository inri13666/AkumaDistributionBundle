<?php

namespace Akuma\Bundle\DistributionBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PreInstallStepEvent extends Event
{
    const NAME = 'akuma.pre_install_step';
}
