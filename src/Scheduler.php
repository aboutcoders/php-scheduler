<?php

namespace Abc\Scheduler;

use Abc\Scheduler\Context\CheckSchedule;
use Abc\Scheduler\Context\End;
use Abc\Scheduler\Context\InitLogger;
use Abc\Scheduler\Context\PreIterateProviders;
use Abc\Scheduler\Context\PreProvideSchedules;
use Abc\Scheduler\Context\PreProcessSchedule;
use Abc\Scheduler\Context\ScheduleProcessed;
use Abc\Scheduler\Context\Start;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Scheduler iterates over schedules and invokes the schedule processor if the schedule is due.
 */
class Scheduler
{
    /**
     * @var BoundProcessorRegistry[]
     */
    private $registry = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $staticExtension;

    public function __construct(
        ExtensionInterface $extension = null,
        array $boundProcessors = [],
        LoggerInterface $logger = null
    ) {
        $this->staticExtension = $extension ?: new ChainExtension([]);
        $this->registry = new BoundProcessorRegistry($boundProcessors);
        $this->logger = $logger ?: new NullLogger();
    }

    public function bindProcessor(ProviderInterface $provider, ProcessorInterface $processor)
    {
        $this->registry->add(new BoundProcessor($provider, $processor));
    }

    /**
     * {@inheritDoc}
     */
    public final function schedule(ExtensionInterface $runtimeExtension = null)
    {
        $extension = $runtimeExtension ? new ChainExtension([
            $this->staticExtension,
            $runtimeExtension,
        ]) : $this->staticExtension;

        $initLogger = new InitLogger($this->logger);
        $extension->onInitLogger($initLogger);

        $this->logger = $initLogger->getLogger();

        $startTime = (int) (microtime(true) * 1000);

        $startContext = new Start($this->logger, $startTime);
        $extension->onStart($startContext);

        if ($startContext->isExecutionInterrupted()) {
            $this->onEnd($extension, $startTime, $startContext->getExitStatus());

            return;
        }

        if (0 == $this->registry->count()) {
            throw new \LogicException('There is nothing to schedule. It is required to bind something before calling schedule method.');
        }

        while (true) {
            $cycle = 1;

            $preIterateProvidersContext = new PreIterateProviders($this->registry->getProviderNames(), $this->logger);
            $extension->onPreIterateProviders($preIterateProvidersContext);

            foreach ($this->registry->all($preIterateProvidersContext->getProviderNames()) as $boundProcessor) {

                $provider = $boundProcessor->getProvider();

                $this->logger->debug(sprintf('[Scheduler] Iterate over schedules of provider "%s"', $provider->getName()));

                $preProvideSchedules = new PreProvideSchedules($provider, $this->logger);
                $extension->onPreProvideSchedules($preProvideSchedules);

                if ($preProvideSchedules->isExecutionInterrupted()) {
                    $this->onEnd($extension, $startTime, $preProvideSchedules->getExitStatus());

                    return;
                }

                $schedules = $provider->provideSchedules($preProvideSchedules->getLimit(), $preProvideSchedules->getOffset());
                foreach ($schedules as $schedule) {

                    $checkSchedule = new CheckSchedule($boundProcessor->getProvider(), $schedule, $this->logger);
                    $extension->onCheckSchedule($checkSchedule);

                    if ($checkSchedule->isInterruptProvider()) {
                        break;
                    }

                    if ($checkSchedule->isDue()) {
                        $preProcessSchedule = new PreProcessSchedule($cycle, $startTime, $this->logger);
                        $extension->onPreProcessSchedule($preProcessSchedule);

                        if ($preProcessSchedule->isExecutionInterrupted()) {
                            $this->onEnd($extension, $startTime, $preProcessSchedule->getExitStatus());

                            return;
                        }

                        try {

                            $boundProcessor->getProcessor()->process($schedule);
                        } catch (\Exception $exception) {
                            $this->onProcessException($extension, $exception);
                        }
                    }

                    $scheduleProcessed = new ScheduleProcessed($cycle, $startTime, $this->logger);
                    $extension->onScheduleProcessed($scheduleProcessed);

                    if ($scheduleProcessed->isExecutionInterrupted()) {
                        $this->onEnd($extension, $startTime, $scheduleProcessed->getExitStatus());

                        return;
                    }

                    ++$cycle;
                }
            }
        }
    }

    private function onEnd(ExtensionInterface $extension, int $startTime, ?int $exitStatus = null): void
    {
        $endTime = (int) (microtime(true) * 1000);

        $endContext = new End($startTime, $endTime, $this->logger, $exitStatus);
        $extension->onEnd($endContext);
    }

    private function onProcessException(ExtensionInterface $extension, Exception $exception)
    {
        // consider passing the exception to the an extension
        throw $exception;
    }
}
