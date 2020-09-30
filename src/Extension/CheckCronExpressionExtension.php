<?php

namespace Abc\Scheduler\Extension;

use Abc\Scheduler\CheckScheduleExtensionInterface;
use Abc\Scheduler\ConcurrencyPolicy;
use Abc\Scheduler\Context\CheckSchedule;
use Cron\CronExpression;

class CheckCronExpressionExtension implements CheckScheduleExtensionInterface
{
    /**
     * @param  \Abc\Scheduler\Context\CheckSchedule  $context
     */
    public function onCheckSchedule(CheckSchedule $context): void
    {
        $schedule = $context->getSchedule();
        $expression = $schedule->getSchedule();

        if (!CronExpression::isValidExpression($expression)) {
            $context->getLogger()->debug(
                sprintf(
                    '[CheckCronExpressionsExtension] Skip invalid cron "%s"',
                    $expression
                )
            );
            return;
        }

        $cron = CronExpression::factory($expression);
        $now = $context->time() ?: time();

        if (!$cron->isDue(new \DateTime("@$now"))) {
            $context->getLogger()->debug(
                sprintf(
                    '[CheckCronExpressionsExtension] Schedule "%s" is not due',
                    $expression
                )
            );
            $context->setDue(false);
            return;
        }

        // ensure that a schedule is not executed twice within 60 seconds
        if (null != $schedule->getScheduledTime()) {
            $timeDate = new \DateTime("@$now");
            $scheduleDate = new \DateTime(sprintf('@%s', $schedule->getScheduledTime()));

            if ($timeDate->format('Y-m-d H:i') == $scheduleDate->format('Y-m-d H:i')) {
                $context->getLogger()->debug(
                    sprintf(
                        '[CheckCronExpressionsExtension] Schedule "%s" is not due since last scheduled at %s',
                        $expression,
                        $scheduleDate->format('Y-m-d H:i')
                    )
                );
                $context->setDue(false);

                return;
            }
        }

        // apply concurrency policy
        if (ConcurrencyPolicy::FORBID() == $schedule->getConcurrencyPolicy() && $context->getProvider(
            )->existsConcurrent($schedule)) {
            $context->getLogger()->warning(
                sprintf('[CheckCronExpressionsExtension] Skip schedule since concurrent schedule exists: %s', $schedule->getConcurrencyPolicy())
            );
            $context->setDue(false);

            return;
        }

        $context->setDue(true);
        $schedule->setScheduledTime($now);

        $context->getLogger()->debug(
            sprintf(
                '[CheckCronExpressionsExtension] Schedule with expression "%s" is due',
                $expression
            )
        );

        return;
    }
}
