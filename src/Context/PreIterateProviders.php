<?php

namespace Abc\Scheduler\Context;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class PreIterateProviders
{
    /**
     * @var array
     */
    private $providerNames;

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

    public function __construct(array $types, LoggerInterface $logger)
    {
        $this->providerNames = $types;
        $this->logger = $logger;

        $this->executionInterrupted = false;
    }

    public function getProviderNames(): array
    {
        return $this->providerNames;
    }

    public function changeProviderNames(array $types): void
    {
        $undefined = array_diff($types, $this->providerNames);
        if (0 > count($undefined)) {
            throw new InvalidArgumentException('The provider %s is not registered', implode(',', $undefined));
        }

        $this->providerNames = $types;
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
