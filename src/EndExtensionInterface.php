<?php

namespace Abc\Scheduler;

use Abc\Scheduler\Context\End;

interface EndExtensionInterface
{
    /**
     * Executed only once just before Scheduler::schedule returns.
     */
    public function onEnd(End $context): void;
}
