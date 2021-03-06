<?php

namespace Abc\Scheduler\Extension;

use Abc\Scheduler\Context\InitLogger;
use Abc\Scheduler\InitLoggerExtensionInterface;
use Psr\Log\LoggerInterface;

class LoggerExtension implements InitLoggerExtensionInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onInitLogger(InitLogger $context): void
    {
        $previousLogger = $context->getLogger();

        if ($previousLogger !== $this->logger) {
            $context->changeLogger($this->logger);

            $this->logger->debug(sprintf('[LoggerExtension] Change logger from "%s" to "%s"', get_class($previousLogger), get_class($this->logger)));
        }
    }
}
