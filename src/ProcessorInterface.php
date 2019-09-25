<?php

namespace Abc\Scheduler;

/**
 * Process a schedule that is due.
 */
interface ProcessorInterface
{
    public function process(ScheduleInterface $schedule): void;
}
