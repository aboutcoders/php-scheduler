<?php

namespace Abc\Scheduler;

class BoundProcessor
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * @var ProcessorInterface
     */
    private $processor;

    public function __construct(ProviderInterface $provider, ProcessorInterface $processor)
    {
        $this->provider = $provider;
        $this->processor = $processor;
    }

    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }

    public function getProcessor(): ProcessorInterface
    {
        return $this->processor;
    }
}
