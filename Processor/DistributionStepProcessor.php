<?php

namespace Akuma\Bundle\DistributionBundle\Processor;

use Akuma\Bundle\DistributionBundle\Event\PostInstallEvent;
use Akuma\Bundle\DistributionBundle\Event\PostInstallStepEvent;
use Akuma\Bundle\DistributionBundle\Event\PreInstallEvent;
use Akuma\Bundle\DistributionBundle\Event\PreInstallStepEvent;
use Akuma\Bundle\DistributionBundle\Registry\DistributionRegistry;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DistributionStepProcessor
{
    /** @var DistributionRegistry */
    protected $registry;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /**
     * @param DistributionRegistry $registry
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(DistributionRegistry $registry, EventDispatcherInterface $dispatcher)
    {
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function install(InputInterface $input, OutputInterface $output)
    {
        $event = new PreInstallEvent($this->registry->getInstallSteps());
        $this->dispatcher->dispatch(PreInstallEvent::NAME, $event);
        $steps = $event->getSteps();
        foreach ($steps as $step) {
            $event = new PreInstallStepEvent($step);
            $this->dispatcher->dispatch(PreInstallStepEvent::NAME, $event);
            $result = $step->execute($input, $output);
            $event = new PostInstallStepEvent($step->getStepName(), $result);
            $this->dispatcher->dispatch(PostInstallStepEvent::NAME, $event);
        }
        $event = new PostInstallEvent();
        $this->dispatcher->dispatch(PostInstallEvent::NAME, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function update(InputInterface $input, OutputInterface $output)
    {
        // TODO: Implement update() method.
    }
}
