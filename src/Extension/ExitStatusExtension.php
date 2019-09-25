<?php

namespace Abc\Scheduler\Extension;

use Abc\Scheduler\Context\End;
use Abc\Scheduler\EndExtensionInterface;

class ExitStatusExtension implements EndExtensionInterface
{
    /**
     * @var int
     */
    private $exitStatus;

    public function onEnd(End $context): void
    {
        $this->exitStatus = $context->getExitStatus();
    }

    public function getExitStatus(): ?int
    {
        return $this->exitStatus;
    }
}
