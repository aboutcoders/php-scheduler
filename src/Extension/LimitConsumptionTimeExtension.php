<?php

namespace Abc\Scheduler\Extension;

use Abc\Scheduler\Context\PreProcessSchedule;
use Abc\Scheduler\Context\PreProvideSchedules;
use Abc\Scheduler\Context\ScheduleProcessed;
use Abc\Scheduler\PreProcessScheduleExtensionInterface;
use Abc\Scheduler\PreProvideSchedulesExtensionInterface;
use Abc\Scheduler\ScheduleProcessedExtensionInterface;
use Psr\Log\LoggerInterface;

class LimitConsumptionTimeExtension implements PreProcessScheduleExtensionInterface, ScheduleProcessedExtensionInterface, PreProvideSchedulesExtensionInterface
{
    /**
     * @var \DateTime
     */
    protected $timeLimit;

    /**
     * @param \DateTime $timeLimit
     */
    public function __construct(\DateTime $timeLimit)
    {
        $this->timeLimit = $timeLimit;
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
        $now = new \DateTime();
        if ($now >= $this->timeLimit) {
            $logger->debug(sprintf(
                '[LimitConsumptionTimeExtension] Execution interrupted as limit time has passed.'.
                ' now: "%s", time-limit: "%s"',
                $now->format(DATE_ISO8601),
                $this->timeLimit->format(DATE_ISO8601)
            ));

            return true;
        }

        return false;
    }
}
