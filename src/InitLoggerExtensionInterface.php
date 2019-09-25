<?php

namespace Abc\Scheduler;

use Abc\Scheduler\Context\InitLogger;

interface InitLoggerExtensionInterface
{
    /**
     * Executed only once at the very beginning of the Scheduler::schedule method call.
     * BEFORE onStart extension method.
     */
    public function onInitLogger(InitLogger $context): void;
}
