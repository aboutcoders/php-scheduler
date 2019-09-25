<?php

namespace Abc\Scheduler;

use Abc\Scheduler\Context\PreProcessSchedule;

interface PreProcessScheduleExtensionInterface
{
    /**
     * Executed before ProcessorInterface::process
     */
    public function onPreProcessSchedule(PreProcessSchedule $context): void;
}
