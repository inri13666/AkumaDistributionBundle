<?php

namespace Akuma\Bundle\DistributionBundle\Registry;

use Akuma\Bundle\DistributionBundle\Model\InstallStepInterface;
use Akuma\Bundle\DistributionBundle\Model\UpdateStepInterface;

class DistributionRegistry
{
    /** @var InstallStepInterface[] */
    protected $installSteps = [];

    /** @var UpdateStepInterface[] */
    protected $updateSteps = [];

    /**
     * @param InstallStepInterface $step
     */
    public function addInstallStep(InstallStepInterface $step)
    {
        $this->installSteps[$step->getStepName()] = $step;
    }

    /**
     * @param UpdateStepInterface $step
     */
    public function addUpdateStep(UpdateStepInterface $step)
    {
        $this->updateSteps[$step->getStepName()] = $step;
    }

    /**
     * @return InstallStepInterface[]
     */
    public function getInstallSteps()
    {
        return $this->installSteps;
    }

    /**
     * @return UpdateStepInterface[]
     */
    public function getUpdateSteps()
    {
        return $this->updateSteps;
    }
}
