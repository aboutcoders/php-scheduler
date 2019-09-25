<?php

namespace Abc\Scheduler\Context;

use Abc\Scheduler\ProviderInterface;
use Abc\Scheduler\ScheduleInterface;
use Psr\Log\LoggerInterface;

class CheckSchedule
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * @var ScheduleInterface
     */
    private $schedule;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $time;

    /**
     * @var bool
     */
    private $due;

    /**
     * @var bool
     */
    private $interruptProvider;

    public function __construct(ProviderInterface $provider, ScheduleInterface $schedule, LoggerInterface $logger)
    {
        $this->provider = $provider;
        $this->schedule = $schedule;
        $this->logger = $logger;

        $this->interruptProvider = false;
    }

    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }

    public function getSchedule(): ScheduleInterface
    {
        return $this->schedule;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function isDue(): ?bool
    {
        return $this->due;
    }

    public function setDue(bool $due)
    {
        $this->due = $due;
    }

    public function time(): ?int
    {
        return $this->time;
    }

    public function changeTime(int $timestamp)
    {
        $this->time = $timestamp;
    }

    public function interruptProvider()
    {
        $this->interruptProvider = true;
    }

    public function isInterruptProvider(): bool
    {
        return $this->interruptProvider;
    }
}
