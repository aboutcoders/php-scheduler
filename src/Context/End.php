<?php

namespace Abc\Scheduler\Context;

use Psr\Log\LoggerInterface;

class End
{
    /**
     * @var int
     */
    private $startTime;

    /**
     * @var int
     */
    private $endTime;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $exitStatus;

    public function __construct(
        int $startTime,
        int $endTime,
        LoggerInterface $logger,
        ?int $exitStatus = null
    ) {
        $this->logger = $logger;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->exitStatus = $exitStatus;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * In milliseconds.
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    /**
     * In milliseconds.
     */
    public function getEndTime(): int
    {
        return $this->startTime;
    }

    public function getExitStatus(): ?int
    {
        return $this->exitStatus;
    }
}
