<?php

namespace Abc\Scheduler\Symfony;

use Abc\Scheduler\ChainExtension;
use Abc\Scheduler\Extension\ExitStatusExtension;
use Abc\Scheduler\Scheduler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScheduleCommand extends Command
{
    use LimitExtensionsCommandTrait;
    use ChooseLoggerCommandTrait;
    use LimitProvidersCommandTrait;

    protected static $defaultName = 'abc:schedule';

    /**
     * @var Scheduler
     */
    private $scheduler;

    public function __construct(Scheduler $scheduler)
    {
        $this->scheduler = $scheduler;
        parent::__construct(static::$defaultName);
    }

    protected function configure(): void
    {
        $this->configureLimitsExtensions();
        $this->configureProviderExtensions();
        $this->configureLoggerExtension();

        $this
            ->setDescription('A scheduler that processes due schedules. '.
                'To use this scheduler you have to bind processors to providers')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $extensions = $this->getLimitsExtensions($input, $output);

        if ($loggerExtension = $this->getLoggerExtension($input, $output)) {
            array_unshift($extensions, $loggerExtension);
        }

        $exitStatusExtension = new ExitStatusExtension();
        array_unshift($extensions, $exitStatusExtension);

        $this->scheduler->schedule(new ChainExtension($extensions));

        return $exitStatusExtension->getExitStatus();
    }
}
