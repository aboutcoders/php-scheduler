<?php

namespace Abc\Scheduler\Symfony;

use Abc\Scheduler\Extension\SingleIterationExtension;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

trait NoExitExtensionCommandTrait
{
    /**
     * {@inheritdoc}
     */
    protected function configureNoExitExtension()
    {
        $this->addOption('no-exit', 'd', InputOption::VALUE_NONE, 'Continues iterating over schedules forever');
    }

    protected function getNoExitExtension(InputInterface $input): ?SingleIterationExtension
    {
        if ($input->getOption('no-exit')) {
            return null;
        }

        return new SingleIterationExtension();
    }
}
