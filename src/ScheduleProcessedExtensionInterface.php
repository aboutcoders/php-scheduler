<?php

namespace Abc\Scheduler;

use Abc\Scheduler\Context\ScheduleProcessed;

interface ScheduleProcessedExtensionInterface
{
    /**
     * Executed after every successful processing of a schedule
     */
    public function onScheduleProcessed(ScheduleProcessed $context): void;
}
