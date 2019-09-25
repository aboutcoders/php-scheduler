<?php

namespace Abc\Scheduler;

use Abc\Scheduler\Context\PreProvideSchedules;

interface PreProvideSchedulesExtensionInterface
{
    /**
     * Executed before every ProviderInterface::provideSchedules
     */
    public function onPreProvideSchedules(PreProvideSchedules $context): void;
}
