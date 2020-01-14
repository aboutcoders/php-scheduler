<?php

namespace Abc\Scheduler\Symfony;

use Abc\Scheduler\Extension\LimitConsumptionTimeExtension;
use Abc\Scheduler\Extension\LimitMemoryExtension;
use Abc\Scheduler\Extension\LimitProcessedSchedulesExtension;
use Abc\Scheduler\Extension\NicenessExtension;
use Abc\Scheduler\ExtensionInterfaceInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

trait LimitExtensionsCommandTrait
{
    protected function configureLimitsExtensions()
    {
        $this
            ->addOption('schedule-limit', null, InputOption::VALUE_REQUIRED, 'Process n schedules and exit')
            ->addOption('time-limit', null, InputOption::VALUE_REQUIRED, 'Process schedules during this time')
            ->addOption('memory-limit', null, InputOption::VALUE_REQUIRED, 'Process schedules until process reaches this memory limit in MB')
            ->addOption('niceness', null, InputOption::VALUE_REQUIRED, 'Set process niceness');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return ExtensionInterfaceInterface[]
     * @throws \Exception
     *
     */
    protected function getLimitsExtensions(InputInterface $input, OutputInterface $output)
    {
        $extensions = [];

        $messageLimit = (int) $input->getOption('schedule-limit');
        if ($messageLimit) {
            $extensions[] = new LimitProcessedSchedulesExtension($messageLimit);
        }

        $timeLimit = $input->getOption('time-limit');
        if ($timeLimit) {
            try {
                $timeLimit = new \DateTime($timeLimit);
            } catch (\Exception $e) {
                $output->writeln('<error>Invalid time limit</error>');

                throw $e;
            }

            $extensions[] = new LimitConsumptionTimeExtension($timeLimit);
        }

        $memoryLimit = (int) $input->getOption('memory-limit');
        if ($memoryLimit) {
            $extensions[] = new LimitMemoryExtension($memoryLimit);
        }

        $niceness = $input->getOption('niceness');
        if (!empty($niceness) && is_numeric($niceness)) {
            $extensions[] = new NicenessExtension((int) $niceness);
        }

        return $extensions;
    }
}

