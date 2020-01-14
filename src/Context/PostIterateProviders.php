<?php

namespace Abc\Scheduler\Context;

use Psr\Log\LoggerInterface;

class PostIterateProviders
{
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

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->executionInterrupted = false;
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
