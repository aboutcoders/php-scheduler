<?php

namespace Abc\Scheduler;

use Abc\Scheduler\Context\CheckSchedule;
use Abc\Scheduler\Context\End;
use Abc\Scheduler\Context\InitLogger;
use Abc\Scheduler\Context\PreIterateProviders;
use Abc\Scheduler\Context\PreProcessSchedule;
use Abc\Scheduler\Context\PreProvideSchedules;
use Abc\Scheduler\Context\ScheduleProcessed;
use Abc\Scheduler\Context\Start;

class ChainExtension implements ExtensionInterface
{
    private $startExtensions;

    private $initLoggerExtensions;

    private $preIterateProvidersExtension;

    private $preProvideSchedulesExtensions;

    private $checkScheduleExtensions;

    private $preProcessScheduleExtensions;

    private $scheduleProcessedExtensions;

    private $endExtensions;

    public function __construct(array $extensions)
    {
        $this->startExtensions = [];
        $this->initLoggerExtensions = [];
        $this->preIterateProvidersExtension = [];
        $this->preProvideSchedulesExtensions = [];
        $this->checkScheduleExtensions = [];
        $this->preProcessScheduleExtensions = [];
        $this->scheduleProcessedExtensions = [];
        $this->endExtensions = [];

        array_walk($extensions, function ($extension) {
            if ($extension instanceof ExtensionInterface) {
                $this->startExtensions[] = $extension;
                $this->initLoggerExtensions[] = $extension;
                $this->preIterateProvidersExtension[] = $extension;
                $this->preProvideSchedulesExtensions[] = $extension;
                $this->checkScheduleExtensions[] = $extension;
                $this->preProcessScheduleExtensions[] = $extension;
                $this->scheduleProcessedExtensions[] = $extension;
                $this->endExtensions[] = $extension;

                return;
            }

            $extensionValid = false;
            if ($extension instanceof StartExtensionInterface) {
                $this->startExtensions[] = $extension;

                $extensionValid = true;
            }

            if ($extension instanceof InitLoggerExtensionInterface) {
                $this->initLoggerExtensions[] = $extension;

                $extensionValid = true;
            }

            if ($extension instanceof PreIterateProvidersExtensionInterface) {
                $this->preIterateProvidersExtension[] = $extension;

                $extensionValid = true;
            }

            if ($extension instanceof PreProvideSchedulesExtensionInterface) {
                $this->preProvideSchedulesExtensions[] = $extension;

                $extensionValid = true;
            }

            if ($extension instanceof CheckScheduleExtensionInterface) {
                $this->checkScheduleExtensions[] = $extension;

                $extensionValid = true;
            }

            if ($extension instanceof PreProcessScheduleExtensionInterface) {
                $this->preProcessScheduleExtensions[] = $extension;

                $extensionValid = true;
            }

            if ($extension instanceof ScheduleProcessedExtensionInterface) {
                $this->scheduleProcessedExtensions[] = $extension;

                $extensionValid = true;
            }

            if ($extension instanceof EndExtensionInterface) {
                $this->endExtensions[] = $extension;

                $extensionValid = true;
            }

            if (false == $extensionValid) {
                throw new \LogicException(sprintf('Invalid extension given %s', get_class($extension)));
            }
        });
    }

    public function onStart(Start $context): void
    {
        foreach ($this->startExtensions as $extension) {
            $extension->onStart($context);
        }
    }

    public function onInitLogger(InitLogger $context): void
    {
        foreach ($this->initLoggerExtensions as $extension) {
            $extension->onInitLogger($context);
        }
    }

    public function onPreIterateProviders(PreIterateProviders $context): void
    {
        foreach ($this->preIterateProvidersExtension as $extension) {
            $extension->onPreIterateProviders($context);
        }
    }

    public function onPreProvideSchedules(PreProvideSchedules $context): void
    {
        foreach ($this->preProvideSchedulesExtensions as $extension) {
            $extension->onPreProvideSchedules($context);
        }
    }

    public function onCheckSchedule(CheckSchedule $context): void
    {
        foreach ($this->checkScheduleExtensions as $extension) {
            $extension->onCheckSchedule($context);
        }
    }

    public function onPreProcessSchedule(PreProcessSchedule $context): void
    {
        foreach ($this->preProcessScheduleExtensions as $extension) {
            $extension->onPreProcessSchedule($context);
        }
    }

    public function onScheduleProcessed(ScheduleProcessed $context): void
    {
        foreach ($this->scheduleProcessedExtensions as $extension) {
            $extension->onScheduleProcessed($context);
        }
    }

    public function onEnd(End $context): void
    {
        foreach ($this->endExtensions as $extension) {
            $extension->onEnd($context);
        }
    }
}
