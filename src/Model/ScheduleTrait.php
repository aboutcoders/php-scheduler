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

    public function setSchedule(string $schedule)
    {
        $this->schedule = $schedule;
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    public function setScheduledTime(int $timestamp)
    {
        $this->scheduledTime = $timestamp;
    }

    public function getScheduledTime(): ?int
    {
        return $this->scheduledTime;
    }
}
