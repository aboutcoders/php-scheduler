<?php

namespace Abc\Scheduler;

class NullProcessor implements ProcessorInterface
{
    public function process(ScheduleInterface $schedule): void
    {
    }
}
