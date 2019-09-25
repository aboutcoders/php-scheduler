<?php

namespace Abc\Scheduler\Context;

use Psr\Log\LoggerInterface;

class PreProcessSchedule
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $cycle;

    /**
     * @var int
     */
    private $startTime;

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
        $this->logger = $logger;
        $this->cycle = $cycle;
        $this->startTime = $startTime;

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
