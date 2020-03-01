<?php

namespace Abc\Scheduler\Tests\Extension;

use Abc\Scheduler\Context\CheckSchedule;
use Abc\Scheduler\Extension\CheckCronExpressionExtension;
use Abc\Scheduler\ProviderInterface;
use Abc\Scheduler\ScheduleInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class CheckCronExpressionExtensionTest extends TestCase
{
    /**
     * @var ProviderInterface|MockObject
     */
    private $providerMock;

    /**
     * @var ScheduleInterface|MockObject
     */
    private $scheduleMock;

    public function setUp(): void
    {
        $this->providerMock = $this->createMock(ProviderInterface::class);
        $this->scheduleMock = $this->createMock(ScheduleInterface::class);
    }

    /**
     * @test
     */
    public function onCheckScheduleSkipsInvalidExpression()
    {
        $subject = new CheckCronExpressionExtension();

        $context = new CheckSchedule($this->providerMock, $this->scheduleMock, new NullLogger());

        $this->scheduleMock->expects($this->any())->method('getSchedule')->willReturn('invalidCron');

        $subject->onCheckSchedule($context);

        $this->assertNull($context->isDue());
    }

    /**
     * @test
     */
    public function onCheckScheduleSetsDueToFalse()
    {
        $subject = new CheckCronExpressionExtension();

        $context = new CheckSchedule($this->providerMock, $this->scheduleMock, new NullLogger());

        $this->scheduleMock->expects($this->any())->method('getSchedule')->willReturn('0 0 1 1 1');

        $subject->onCheckSchedule($context);

        $this->assertFalse($context->isDue());
    }

    /**
     * @test
     */
    public function onCheckScheduleSetsDueToTrue()
    {
        $subject = new CheckCronExpressionExtension();

        $context = new CheckSchedule($this->providerMock, $this->scheduleMock, new NullLogger());

        $this->scheduleMock->expects($this->any())->method('getSchedule')->willReturn('* * * * *');

        $subject->onCheckSchedule($context);

        $this->assertTrue($context->isDue());
    }

    /**
     * @test
     * @dataProvider provideDatePairs
     */
    public function onCheckSetsDueToFalseIfScheduledSameMinute(int $time, int $scheduled, bool $isDue)
    {
        $subject = new CheckCronExpressionExtension();

        $context = new CheckSchedule($this->providerMock, $this->scheduleMock, new NullLogger());
        $context->changeTime($time);

        $this->scheduleMock->expects($this->any())->method('getSchedule')->willReturn('* * * * *');
        $this->scheduleMock->expects($this->any())->method('getScheduledTime')->willReturn($scheduled);

        $subject->onCheckSchedule($context);

        $this->assertEquals($isDue, $context->isDue());
    }

    public static function provideDatePairs(): array
    {
        return [
            // time                            // scheduled
            [strtotime('2000-10-01 06:00:01'), strtotime('2000-10-01 06:00:00'), false],
            [strtotime('2000-10-01 06:00:01'), strtotime('2000-10-01 06:00:59'), false],
            [strtotime('2000-10-01 05:59:59'), strtotime('2000-10-01 06:00:00'), true],
            [strtotime('2000-10-01 05:59:59'), strtotime('2000-10-01 06:00:01'), true],
        ];
    }
}
