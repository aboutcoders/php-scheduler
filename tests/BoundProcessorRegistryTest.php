<?php

namespace Abc\Scheduler\Tests;

use Abc\Scheduler\BoundProcessor;
use Abc\Scheduler\BoundProcessorRegistry;
use Abc\Scheduler\ProcessorInterface;
use Abc\Scheduler\ProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BoundProcessorRegistryTest extends TestCase
{
    public function testGetProviderNames()
    {
        $subject = new BoundProcessorRegistry();
        $subject->add($this->createBoundProcessor('A'));
        $subject->add($this->createBoundProcessor('B'));

        $this->assertEquals(['A', 'B'], $subject->getProviderNames());
    }

    /**
     * @dataProvider provideFilterNamesData
     */
    public function testAll(array $existing, array $filter, $expected)
    {
        $subject = new BoundProcessorRegistry();

        $processors = [];
        foreach ($existing as $name) {
            $processor = $this->createBoundProcessor($name);
            $subject->add($processor);
            $processors[$name] = $processor;
        }

        if(empty($expected))
        {
            $this->assertEmpty($subject->all($filter));

            return;
        }

        $filtered = $subject->all($filter);
        foreach ($expected as $name) {
            $this->assertContains($processors[$name], $filtered);
        }
    }

    public static function provideFilterNamesData(): array
    {
        return [
            [$existing = [], $filter = ['A'], $expected = []],
            [$existing = ['A', 'B'], $filter = [], $expected = ['A', 'B']],
            [$existing = ['A', 'B', 'C'], $filter = ['A'], $expected = ['A']],
            [$existing = ['A', 'B', 'C'], $filter = ['A', 'B'], $expected = ['A', 'B']],
            [$existing = ['A', 'B', 'C'], $filter = ['B', 'C'], $expected = ['B', 'C']],
            [$existing = ['A', 'B', 'C'], $filter = ['A', 'C'], $expected = ['A', 'C']],
            [$existing = ['A', 'B', 'C'], $filter = ['A', 'D'], $expected = ['A']],
        ];
    }

    private function createBoundProcessor(string $providerName)
    {
        $provider = $this->createProviderMock($providerName);

        /** @var ProcessorInterface $processor */
        $processor = $this->createMock(ProcessorInterface::class);

        return new BoundProcessor($provider, $processor);
    }

    private function createProviderMock(string $name): MockObject
    {
        $provider = $this->createMock(ProviderInterface::class);
        $provider->expects($this->any())->method('getName')->willReturn($name);

        return $provider;
    }
}
