<?php

namespace Abc\Scheduler\Extension;

use Abc\Scheduler\Context\PostIterateProviders;
use Abc\Scheduler\PostIterateProvidersExtensionInterface;
use Psr\Log\LoggerInterface;

class SingleIterationExtension implements PostIterateProvidersExtensionInterface
{
    public function onPostIterateProviders(PostIterateProviders $context): void
    {
        if ($this->shouldBeStopped($context->getLogger())) {
            $context->interruptExecution();
        }
    }

    private function shouldBeStopped(LoggerInterface $logger): bool
    {
        return true;
    }
}
