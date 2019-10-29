<?php

namespace Abc\Scheduler\Model;

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
}
