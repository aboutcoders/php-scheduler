<?php

namespace Abc\Scheduler;

class BoundProcessorRegistry implements \Countable
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;

    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @var BoundProcessor[]
     */
    private $boundProcessors;

    /**
     * @param BoundProcessor[] $boundProcessors
     */
    public function __construct(array $boundProcessors = [])
    {
        $this->providers = [];
        $this->processors = [];
        $this->boundProcessors = [];

        foreach ($boundProcessors as $boundProcessor) {
            $this->add($boundProcessor);
        }
    }

    public function add(BoundProcessor $boundProcessor)
    {
        $name = $boundProcessor->getProvider()->getName();
        $this->providers[$name] = $boundProcessor->getProvider();
        $this->processors[$name] = $boundProcessor->getProcessor();
        $this->boundProcessors[$name] = $boundProcessor;
    }

    public function getProviderNames(): array
    {
        return array_keys($this->providers);
    }

    public function getProvider($name): ?ProviderInterface
    {
        return $this->providers[$name] ?? null;
    }

    public function getProcessor($name): ?ProcessorInterface
    {
        return $this->processors[$name] ?? null;
    }

    /**
     * @param string[] $providerNames
     * @return BoundProcessor[]
     */
    public function all(array $providerNames = []): array
    {
        return empty($providerNames) ? $this->boundProcessors : array_intersect_key($this->boundProcessors, array_flip($providerNames));
    }

    public function count()
    {
        return count($this->boundProcessors);
    }
}
