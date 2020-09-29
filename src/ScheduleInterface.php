<?php

namespace Abc\Scheduler;

interface ScheduleInterface
{
    /**
     * @return string A schedule expression
     */
    public function getSchedule(): string;

    public function setScheduledTime(int $timestamp);

    public function getScheduledTime(): ?int;

    public function getConcurrencyPolicy(): ConcurrencyPolicy;
}
