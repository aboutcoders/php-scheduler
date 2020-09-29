<?php

namespace Abc\Scheduler\Model;

use Abc\Scheduler\ConcurrencyPolicy;

trait ScheduleTrait
{
    /**
     * @var string
     */
    protected $schedule;

    /**
     * @var int
     */
    protected $scheduledTime;

    /**
     * @var ConcurrencyPolicy
     */
    protected $concurrencyPolicy;

    public function setSchedule(string $schedule): void
    {
        $this->schedule = $schedule;
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    public function setScheduledTime(int $timestamp): void
    {
        $this->scheduledTime = $timestamp;
    }

    public function getScheduledTime(): ?int
    {
        return $this->scheduledTime;
    }

    public function getConcurrencyPolicy(): ConcurrencyPolicy
    {
        return $this->concurrencyPolicy ?? ConcurrencyPolicy::ALLOW();
    }

    public function setConcurrencyPolicy(ConcurrencyPolicy $concurrencyPolicy): void
    {
        $this->concurrencyPolicy = $concurrencyPolicy;
    }
}
