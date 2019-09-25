<?php

namespace Abc\Scheduler\Tests;

use Abc\Scheduler\Context\CheckSchedule;
use Abc\Scheduler\Context\InitLogger;
use Abc\Scheduler\Context\PreProcessSchedule;
use Abc\Scheduler\Context\ScheduleProcessed;
use Abc\Scheduler\Context\Start;
use Abc\Scheduler\ExtensionInterface;
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
     * @var ExtensionInterface|MockObject
     */
    private $constructorExtensionMock;

    /** @var Scheduler */
    private $subject;

    public function setUp()
    {
        $this->constructorExtensionMock = $this->createMock(ExtensionInterface::class);
        $this->subject = new Scheduler($this->constructorExtensionMock);
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function scheduleThrowsExceptionIfWithNoProcessorsBound()
    {
        $this->setCycleLimit($this->constructorExtensionMock, 1);

        $runtimeExtensionMock = $this->createMock(ExtensionInterface::class);
        $this->subject->schedule($runtimeExtensionMock);
    }

    /**
     * @test
     */
    public function scheduleCallsProcessorOnDueSchedule()
    {
        $this->setCycleLimit($this->constructorExtensionMock, 1);

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

    public function scheduleInterruptsExecutionOnStart()
    {
        $this->constructorExtensionMock->expects($this->once())->method('onStart')->willReturnCallback($this->getInterruptExecutionCallback());
        $this->constructorExtensionMock->expects($this->never())->method('onPreIterateProviders');

        $this->subject->schedule();
    }

    /**
     * @test
     * @expectedException Exception foobar
     */
    public function scheduleMergesExtensions()
    {
        $this->setCycleLimit($this->constructorExtensionMock, 1);

        $this->constructorExtensionMock->expects($this->once())->method('onInitLogger');

        $runtimeExtension = $this->createMock(ExtensionInterface::class);
        $runtimeExtension->expects($this->once())->method('onInitLogger')->willThrowException(new \Exception('foobar'));

        $this->subject->schedule($runtimeExtension);
    }

    /**
     * @test
     */
    public function scheduleCallsExtensionsInOrder()
    {
        $this->setCycleLimit($this->constructorExtensionMock, 1);

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

        $runtimeExtension = $this->createMock(ExtensionInterface::class);
        $runtimeExtension->expects($this->at(0))->method('onInitLogger')->willReturnCallback($changeLoggerCallback);
        $runtimeExtension->expects($this->at(1))->method('onStart')->with($this->callback($isCustomLoggerCallback));
        $runtimeExtension->expects($this->at(2))->method('onPreIterateProviders')->with($this->callback($isCustomLoggerCallback));
        $runtimeExtension->expects($this->at(3))->method('onPreProvideSchedules')->with($this->callback($isCustomLoggerCallback));
        $runtimeExtension->expects($this->at(4))->method('onCheckSchedule')->with($this->callback($isCustomLoggerCallback))->willReturnCallback($setScheduleDue);
        $runtimeExtension->expects($this->at(5))->method('onPreProcessSchedule')->with($this->callback($isCustomLoggerCallback));
        $runtimeExtension->expects($this->at(6))->method('onScheduleProcessed')->with($this->callback($isCustomLoggerCallback));
        $runtimeExtension->expects($this->at(7))->method('onEnd')->with($this->callback($isCustomLoggerCallback));

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

        $runtimeExtension = $this->createMock(ExtensionInterface::class);
        $runtimeExtension->expects($this->any())->method('onCheckSchedule')->willReturnCallback($setScheduleDue);
        $runtimeExtension->expects($this->at(5))->method('onPreProcessSchedule')->withConsecutive($this->callback($this->getCycleEqualsCallback(1)), $this->callback($this->getCycleEqualsCallback(2)));
        $runtimeExtension->expects($this->at(6))->method('onScheduleProcessed')->withConsecutive($this->callback($this->getCycleEqualsCallback(1)), $this->callback($this->getCycleEqualsCallback(2)));

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
