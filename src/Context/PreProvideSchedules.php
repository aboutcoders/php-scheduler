<?php

namespace Abc\Scheduler\Context;

use Abc\Scheduler\ProviderInterface;
use Psr\Log\LoggerInterface;

class PreProvideSchedules
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $limit = null;

    /**
     * @var int
     */
    private $offset = null;

    /**
     * @var bool
     */
    private $executionInterrupted;

    /**
     * @var int
     */
    private $exitStatus;

    public function __construct(ProviderInterface $provider, LoggerInterface $logger)
    {
        $this->provider = $provider;
        $this->logger = $logger;

        $this->executionInterrupted = false;
    }

    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setLimit(?int $limit)
    {
        $this->limit = $limit;
    }

    public function setOffset(?int $offset)
    {
        $this->offset = $offset;
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
