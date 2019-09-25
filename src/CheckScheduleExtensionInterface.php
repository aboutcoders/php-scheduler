<?php

namespace Abc\Scheduler;

use Abc\Scheduler\Context\CheckSchedule;

interface CheckScheduleExtensionInterface
{
    public function onCheckSchedule(CheckSchedule $context): void;
}
