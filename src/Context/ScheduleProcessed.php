<?php

namespace Abc\Scheduler\Context;

use Psr\Log\LoggerInterface;

class ScheduleProcessed
{
    /**
     * @var int
     */
    private $cycle;

    /**
     * @var int
     */
    private $startTime;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $executionInterrupted;

    /**
     * @var int
     */
    private $exitStatus;

    public function __construct(int $cycle, int $startTime, LoggerInterface $logger)
    {
        $this->cycle = $cycle;
        $this->startTime = $startTime;
        $this->logger = $logger;

        $this->executionInterrupted = false;
    }

    public function getCycle(): int
    {
        return $this->cycle;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getExitStatus(): ?int
    {
        return $this->exitStatus;
    }

    public function isExecutionInterrupted(): bool
    {
        return $this->executionInterrupted;
    }

    public function interruptExecution(?int $exitStatus = null): void
    {
        $this->exitStatus = $exitStatus;
        $this->executionInterrupted = true;
    }
}
