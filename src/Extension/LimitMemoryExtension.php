<?php

namespace Abc\Scheduler\Extension;

use Abc\Scheduler\Context\PreProcessSchedule;
use Abc\Scheduler\Context\PreProvideSchedules;
use Abc\Scheduler\Context\ScheduleProcessed;
use Abc\Scheduler\PreProcessScheduleExtensionInterface;
use Abc\Scheduler\PreProvideSchedulesExtensionInterface;
use Abc\Scheduler\ScheduleProcessedExtensionInterface;
use Psr\Log\LoggerInterface;

class LimitMemoryExtension implements PreProcessScheduleExtensionInterface, ScheduleProcessedExtensionInterface, PreProvideSchedulesExtensionInterface
{
    /**
     * @var int
     */
    protected $memoryLimit;

    /**
     * @param int $memoryLimit Megabytes
     */
    public function __construct($memoryLimit)
    {
        if (false == is_int($memoryLimit)) {
            throw new \InvalidArgumentException(sprintf('Expected memory limit is int but got: "%s"', is_object($memoryLimit) ? get_class($memoryLimit) : gettype($memoryLimit)));
        }

        $this->memoryLimit = $memoryLimit * 1024 * 1024;
    }

    public function onPreProvideSchedules(PreProvideSchedules $context): void
    {
        if ($this->shouldBeStopped($context->getLogger())) {
            $context->interruptExecution();
        }
    }

    public function onPreProcessSchedule(PreProcessSchedule $context): void
    {
        if ($this->shouldBeStopped($context->getLogger())) {
            $context->interruptExecution();
        }
    }

    public function onScheduleProcessed(ScheduleProcessed $context): void
    {
        if ($this->shouldBeStopped($context->getLogger())) {
            $context->interruptExecution();
        }
    }

    protected function shouldBeStopped(LoggerInterface $logger): bool
    {
        $memoryUsage = memory_get_usage(true);
        if ($memoryUsage >= $this->memoryLimit) {
            $logger->debug(sprintf('[LimitMemoryExtension] Interrupt execution as memory limit reached. limit: "%s", used: "%s"', $this->memoryLimit, $memoryUsage));

            return true;
        }

        return false;
    }
}
