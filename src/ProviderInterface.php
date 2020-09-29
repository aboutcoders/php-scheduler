<?php

namespace Abc\Scheduler;

interface ProviderInterface
{
    /**
     * @return string The provider's name, used to bind a provider to processors
     */
    public function getName(): string;

    /**
     * @return ScheduleInterface[]
     */
    public function provideSchedules(int $limit = null, int $offset = null): array;

    public function existsConcurrent(ScheduleInterface $schedule): bool;

    public function save(ScheduleInterface $schedule): void;
}
