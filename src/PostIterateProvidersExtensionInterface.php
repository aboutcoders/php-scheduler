<?php

namespace Abc\Scheduler;

use Abc\Scheduler\Context\PostIterateProviders;

interface PostIterateProvidersExtensionInterface
{
    /**
     * Executed once after iterating over all schedule providers
     */
    public function onPostIterateProviders(PostIterateProviders $context): void;
}
