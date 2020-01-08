<?php

namespace Abc\Scheduler\Tests\Symfony;

use Abc\Scheduler\ChainExtension;
use Abc\Scheduler\Extension\ExitStatusExtension;
use Abc\Scheduler\Scheduler;
use Abc\Scheduler\Symfony\ScheduleCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class ScheduleCommandTest extends TestCase
{
    /**
     * @var Scheduler|MockObject
     */
    private $schedulerMock;

    /**
     * @var ScheduleCommand
     */
    private $subject;

    public function setUp(): void
    {
        $this->schedulerMock = $this->createMock(Scheduler::class);
        $this->subject = new ScheduleCommand($this->schedulerMock);
    }

    public function testExecute()
    {
        $input = new ArrayInput([]);
        $output = new NullOutput();

        $this->schedulerMock->expects($this->once())->method('schedule')->with($this->equalTo(new ChainExtension([new ExitStatusExtension()])));

        $this->assertSame(0, $this->subject->run($input, $output));
    }
}
