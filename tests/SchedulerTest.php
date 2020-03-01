<?php

namespace Abc\Scheduler\Tests;

use Abc\Scheduler\Context\CheckSchedule;
use Abc\Scheduler\Context\InitLogger;
use Abc\Scheduler\Context\PostIterateProviders;
use Abc\Scheduler\Context\PreIterateProviders;
use Abc\Scheduler\Context\PreProcessSchedule;
use Abc\Scheduler\Context\PreProvideSchedules;
use Abc\Scheduler\Context\ScheduleProcessed;
use Abc\Scheduler\Context\Start;
use Abc\Scheduler\ExtensionInterfaceInterface;
use Abc\Scheduler\ProviderInterface;
use Abc\Scheduler\ProcessorInterface;
use Abc\Scheduler\ScheduleInterface;
use Abc\Scheduler\Scheduler;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class SchedulerTest extends TestCase
{
    /**
     * @var ExtensionInterfaceInterface|MockObject
     */
    private $constructorExtensionMock;

    /** @var Scheduler */
    private $subject;

    public function setUp(): void
    {
        $this->constructorExtensionMock = $this->createMock(ExtensionInterfaceInterface::class);
        $this->subject = new Scheduler($this->constructorExtensionMock);
    }

    /**
     * @test
     */
    public function scheduleThrowsExceptionIfWithNoProcessorsBound()
    {
        $this->exitAfterSingleIteration();

        $runtimeExtensionMock = $this->createMock(ExtensionInterfaceInterface::class);

        $this->expectException(\LogicException::class);

        $this->subject->schedule($runtimeExtensionMock);
    }

    /**
     * @test
     */
    public function scheduleCallsProcessorOnDueSchedule()
    {
        $this->exitAfterSingleIteration();

        $schedule = $this->createMock(ScheduleInterface::class);

        $type = 'someType';
        $providerMock = $this->createMock(ProviderInterface::class);
        $providerMock->expects($this->any())->method('getName')->wilLReturn($type);
        $providerMock->expects($this->once())->method('provideSchedules')->willReturn([$schedule]);

        $this->constructorExtensionMock->expects($this->once())->method('onCheckSchedule')->willReturnCallback(function (
            CheckSchedule $context
        ) {
            $context->setDue(true);
        });

        $processorMock = $this->createMock(ProcessorInterface::class);

        $processorMock->expects($this->once())->method('process')->with($schedule);

        $this->subject->bindProcessor($providerMock, $processorMock);

        $this->subject->schedule();
    }

    /**
     * @test
     */
    public function scheduleThrowsProcessorException()
    {
        $this->exitAfterSingleIteration();

        $schedule = $this->createMock(ScheduleInterface::class);

        $type = 'someType';
        $providerMock = $this->createMock(ProviderInterface::class);
        $providerMock->expects($this->any())->method('getName')->wilLReturn($type);
        $providerMock->expects($this->once())->method('provideSchedules')->willReturn([$schedule]);

        $this->constructorExtensionMock->expects($this->once())->method('onCheckSchedule')->willReturnCallback(function (
            CheckSchedule $context
        ) {
            $context->setDue(true);
        });

        $processorMock = $this->createMock(ProcessorInterface::class);

        $processorMock->expects($this->once())->method('process')->willThrowException(new \Exception('foo'));

        $this->subject->bindProcessor($providerMock, $processorMock);

        $this->expectException(\Exception::class);

        $this->subject->schedule();
    }

    /**
     * @test
     */
    public function scheduleInterruptsExecutionOnStart()
    {
        $this->constructorExtensionMock->expects($this->once())->method('onStart')->willReturnCallback($this->getInterruptExecutionCallback());
        $this->constructorExtensionMock->expects($this->never())->method('onPreIterateProviders');

        $this->subject->schedule();
    }

    /**
     * @test
     */
    public function scheduleInterruptsExecutionOnPreIterateProviders()
    {
        $this->exitAfterSingleIteration();

        $schedule = $this->createMock(ScheduleInterface::class);

        $this->constructorExtensionMock->expects($this->any())->method('onPreIterateProviders')->willReturnCallback(function (
            PreIterateProviders $context
        ) {
            $context->interruptExecution();
        });

        $providerMock = $this->createMock(ProviderInterface::class);
        $providerMock->expects($this->never())->method('provideSchedules')->willReturn([$schedule]);

        $this->constructorExtensionMock->expects($this->never())->method('onCheckSchedule');

        $processorMock = $this->createMock(ProcessorInterface::class);

        $processorMock->expects($this->never())->method('process');

        $this->subject->bindProcessor($providerMock, $processorMock);

        $this->subject->schedule();
    }

    /**
     * @test
     */
    public function scheduleInterruptsExecutionOnPreProvideSchedules()
    {
        $this->exitAfterSingleIteration();

        $schedule = $this->createMock(ScheduleInterface::class);

        $providerMock = $this->createMock(ProviderInterface::class);
        $providerMock->expects($this->never())->method('provideSchedules')->willReturn([$schedule]);

        $this->constructorExtensionMock->expects($this->once())->method('onPreProvideSchedules')->willReturnCallback(function (
            PreProvideSchedules $context
        ) {
            $context->interruptExecution();
        });

        $this->constructorExtensionMock->expects($this->never())->method('onCheckSchedule');

        $processorMock = $this->createMock(ProcessorInterface::class);

        $processorMock->expects($this->never())->method('process');

        $this->subject->bindProcessor($providerMock, $processorMock);

        $this->subject->schedule();
    }

    /**
     * @test
     */
    public function scheduleInterruptsExecutionOnPreProcessSchedule()
    {
        $this->exitAfterSingleIteration();

        $schedule = $this->createMock(ScheduleInterface::class);

        $type = 'someType';
        $providerMock = $this->createMock(ProviderInterface::class);
        $providerMock->expects($this->any())->method('getName')->wilLReturn($type);
        $providerMock->expects($this->once())->method('provideSchedules')->willReturn([$schedule]);

        $this->constructorExtensionMock->expects($this->once())->method('onCheckSchedule')->willReturnCallback(function (
            CheckSchedule $context
        ) {
            $context->setDue(true);
        });

        $this->constructorExtensionMock->expects($this->once())->method('onPreProcessSchedule')->willReturnCallback(function (
            PreProcessSchedule $context
        ) {
            $context->interruptExecution();
        });

        $processorMock = $this->createMock(ProcessorInterface::class);

        $processorMock->expects($this->never())->method('process')->with();

        $this->subject->bindProcessor($providerMock, $processorMock);

        $this->subject->schedule();
    }

    /**
     * @test
     */
    public function scheduleMergesExtensions()
    {
        $this->exitAfterSingleIteration();

        $this->constructorExtensionMock->expects($this->once())->method('onInitLogger');

        $runtimeExtension = $this->createMock(ExtensionInterfaceInterface::class);
        $runtimeExtension->expects($this->once())->method('onInitLogger')->willThrowException(new \Exception('foobar'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('foobar');

        $this->subject->schedule($runtimeExtension);
    }

    /**
     * @test
     */
    public function scheduleCallsExtensionsInOrder()
    {
       $this->exitAfterSingleIteration();

        $schedule = $this->createMock(ScheduleInterface::class);

        $type = 'someType';
        $providerMock = $this->createMock(ProviderInterface::class);
        $providerMock->expects($this->any())->method('getName')->wilLReturn($type);
        $providerMock->expects($this->any())->method('provideSchedules')->willReturn([$schedule]);

        $processorMock = $this->createMock(ProcessorInterface::class);
        $processorMock->expects($this->any())->method('process')->with($schedule);

        $this->subject->bindProcessor($providerMock, $processorMock);

        $customLogger = $this->createMock(LoggerInterface::class);
        $changeLoggerCallback = function (InitLogger $context) use ($customLogger) {
            $context->changeLogger($customLogger);
        };

        $isCustomLoggerCallback = function ($context) use ($customLogger) {
            return $customLogger === $context->getLogger();
        };

        $setScheduleDue = function (CheckSchedule $context) {
            $context->setDue(true);
        };

        $runtimeExtension = $this->createMock(ExtensionInterfaceInterface::class);
        $runtimeExtension->expects($this->at(0))->method('onInitLogger')->willReturnCallback($changeLoggerCallback);
        $runtimeExtension->expects($this->at(1))->method('onStart')->with($this->callback($isCustomLoggerCallback));
        $runtimeExtension->expects($this->at(2))->method('onPreIterateProviders')->with($this->callback($isCustomLoggerCallback));
        $runtimeExtension->expects($this->at(3))->method('onPreProvideSchedules')->with($this->callback($isCustomLoggerCallback));
        $runtimeExtension->expects($this->at(4))->method('onCheckSchedule')->with($this->callback($isCustomLoggerCallback))->willReturnCallback($setScheduleDue);
        $runtimeExtension->expects($this->at(5))->method('onPreProcessSchedule')->with($this->callback($isCustomLoggerCallback));
        $runtimeExtension->expects($this->at(6))->method('onScheduleProcessed')->with($this->callback($isCustomLoggerCallback));
        $runtimeExtension->expects($this->at(7))->method('onPostIterateProviders')->with($this->callback($isCustomLoggerCallback));
        $runtimeExtension->expects($this->at(8))->method('onEnd')->with($this->callback($isCustomLoggerCallback));

        $this->subject->schedule($runtimeExtension);
    }

    /**
     * @test
     */
    public function scheduleIncrementsCycleOnEverySchedule()
    {
        $this->setCycleLimit($this->constructorExtensionMock, 2);

        $schedule_A = $this->createMock(ScheduleInterface::class);
        $schedule_B = $this->createMock(ScheduleInterface::class);

        $type = 'someType';
        $providerMock = $this->createMock(ProviderInterface::class);
        $providerMock->expects($this->any())->method('getName')->wilLReturn($type);
        $providerMock->expects($this->any())->method('provideSchedules')->willReturn([$schedule_A, $schedule_B]);

        $processorMock = $this->createMock(ProcessorInterface::class);

        $this->subject->bindProcessor($providerMock, $processorMock);

        $setScheduleDue = function (CheckSchedule $context) {
            $context->setDue(true);
        };;

        $runtimeExtension = $this->createMock(ExtensionInterfaceInterface::class);
        $runtimeExtension->expects($this->any())->method('onCheckSchedule')->willReturnCallback($setScheduleDue);
        $runtimeExtension->expects($this->at(5))->method('onPreProcessSchedule')->withConsecutive([$this->callback($this->getCycleEqualsCallback(1))], [$this->callback($this->getCycleEqualsCallback(2))]);
        $runtimeExtension->expects($this->at(6))->method('onScheduleProcessed')->withConsecutive([$this->callback($this->getCycleEqualsCallback(1))], [$this->callback($this->getCycleEqualsCallback(2))]);

        $this->subject->schedule($runtimeExtension);
    }

    private function setCycleLimit(MockObject $extension, int $limit)
    {
        $callback = function (PreProcessSchedule $context) use ($limit) {
            if ($limit < $context->getCycle()) {
                $context->interruptExecution();
            }
        };

        $extension->expects($this->any())->method('onPreProcessSchedule')->willReturnCallback($callback);

        $callback = function (ScheduleProcessed $context) use ($limit) {
            if ($limit <= $context->getCycle()) {
                $context->interruptExecution();
            }
        };

        $extension->expects($this->any())->method('onScheduleProcessed')->willReturnCallback($callback);
    }

    private function exitAfterSingleIteration() {
        $this->constructorExtensionMock->expects($this->any())->method('onPostIterateProviders')->willReturnCallback(function (
            PostIterateProviders $context
        ) {
            $context->interruptExecution();
        });
    }

    private function getInterruptExecutionCallback() {
        return function ($context) {
            $context->interruptExecution();
        };
    }

    private function getCycleEqualsCallback(int $cycle): \Closure
    {
        return function ($context) use ($cycle) {
            return $cycle === $context->getCycle();
        };
    }
}
