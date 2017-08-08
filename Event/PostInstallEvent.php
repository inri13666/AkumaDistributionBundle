<?php

namespace Akuma\Bundle\DistributionBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PostInstallEvent extends Event
{
    const NAME = 'akuma.post_install';
}
