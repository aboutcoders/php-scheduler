<?php

namespace Abc\Scheduler\Extension;

use Abc\Scheduler\Context\PreIterateProviders;
use Abc\Scheduler\PreIterateProvidersExtensionInterface;

class LimitProvidersExtension implements PreIterateProvidersExtensionInterface
{
    /**
     * @var string[]
     */
    private $providerNames;

    /**
     * @param string[] $providerNames
     */
    public function __construct(array $providerNames)
    {
        $this->providerNames = $providerNames;
    }

    public function onPreIterateProviders(PreIterateProviders $context): void
    {
        $context->getLogger()->debug(sprintf('[LimitProvidersExtension] Changed providers to [%s]', implode('|', $this->providerNames)));

        $context->changeProviderNames($this->providerNames);
    }
}
