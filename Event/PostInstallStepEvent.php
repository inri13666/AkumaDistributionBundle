<?php

namespace Akuma\Bundle\DistributionBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PostInstallStepEvent extends Event
{
    const NAME = 'akuma.post_install_step';
}
