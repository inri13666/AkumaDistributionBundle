<?php

namespace Akuma\Bundle\DistributionBundle\Model;

use Symfony\Component\HttpKernel\KernelInterface;

class Bundle
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var int
     */
    protected $priority = 0;

    /**
     * @var bool
     */
    protected $kernelRequired = false;

    /**
     * @param KernelInterface $kernel
     *
     * @return object
     */
    public function getInstance(KernelInterface $kernel)
    {
        $bundleClass = $this->getClass();

        return $this->isKernelRequired() ? new $bundleClass($kernel) : new $bundleClass;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isKernelRequired()
    {
        return $this->kernelRequired;
    }

    /**
     * @param boolean $kernelRequired
     *
     * @return $this
     */
    public function setKernelRequired($kernelRequired)
    {
        $this->kernelRequired = $kernelRequired;

        return $this;
    }
}
