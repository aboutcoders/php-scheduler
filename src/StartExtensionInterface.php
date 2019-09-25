<?php

namespace Abc\Scheduler;

use Abc\Scheduler\Context\Start;

interface StartExtensionInterface
{
    /**
     * Executed only once at the very beginning of the Scheduler::schedule method call.
     */
    public function onStart(Start $context): void;
}
