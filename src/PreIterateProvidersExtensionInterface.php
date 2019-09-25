<?php

namespace Abc\Scheduler;

use Abc\Scheduler\Context\PreIterateProviders;

interface PreIterateProvidersExtensionInterface
{
    /**
     * Executed once before starting to iterate over schedule providers
     */
    public function onPreIterateProviders(PreIterateProviders $context): void;
}
