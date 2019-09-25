<?php

namespace Abc\Scheduler\Extension;

use Abc\Scheduler\Context\PreProcessSchedule;
use Abc\Scheduler\Context\ScheduleProcessed;
use Abc\Scheduler\PreProcessScheduleExtensionInterface;
use Abc\Scheduler\ScheduleProcessedExtensionInterface;
use Psr\Log\LoggerInterface;

class LimitProcessedSchedulesExtension implements PreProcessScheduleExtensionInterface, ScheduleProcessedExtensionInterface
{
    /**
     * @var int
     */
    protected $scheduleLimit;

    /**
     * @var int
     */
    protected $schedulesProcessed;


    /**
     * @param int $messageLimit
     */
    public function __construct(int $messageLimit)
    {
        $this->scheduleLimit = $messageLimit;
        $this->schedulesProcessed = 0;
    }

    public function onPreProcessSchedule(PreProcessSchedule $context): void
    {
        // this is added here to handle an edge case. when a user sets zero as limit.
        if ($this->shouldBeStopped($context->getLogger())) {
            $context->interruptExecution();
        }
    }

    public function onScheduleProcessed(ScheduleProcessed $context): void
    {
        ++$this->schedulesProcessed;

        if ($this->shouldBeStopped($context->getLogger())) {
            $context->interruptExecution();
        }
    }

    protected function shouldBeStopped(LoggerInterface $logger): bool
    {
        if ($this->schedulesProcessed >= $this->scheduleLimit) {
            $logger->debug(sprintf(
                '[LimitProcessedSchedulesExtension] Schedule processing interrupted since the schedule limit reached.'.
                ' limit: "%s"',
                $this->scheduleLimit
            ));

            return true;
        }

        return false;
    }
}
