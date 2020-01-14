<?php

namespace Abc\Scheduler\Symfony;

use Abc\Scheduler\Extension\LimitConsumptionTimeExtension;
use Abc\Scheduler\Extension\LimitMemoryExtension;
use Abc\Scheduler\Extension\LimitProcessedSchedulesExtension;
use Abc\Scheduler\Extension\LimitProvidersExtension;
use Abc\Scheduler\Extension\NicenessExtension;
use Abc\Scheduler\Extension\ProvidersExtension;
use Abc\Scheduler\ExtensionInterfaceInterface;
use Abc\Scheduler\PreIterateProvidersExtensionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

trait LimitProvidersCommandTrait
{
    protected function configureProviderExtensions()
    {
        $this
            ->addOption('provider', null, InputOption::VALUE_REQUIRED, 'Process schedules of this provider');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     *
     * @return PreIterateProvidersExtensionInterface
     */
    protected function getProviderExtensions(InputInterface $input, OutputInterface $output)
    {
        $providerName = (int) $input->getOption('provider');

        return new LimitProvidersExtension([$providerName]);
    }
}
