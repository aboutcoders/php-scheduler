<?php

namespace Abc\Scheduler;

interface ProviderInterface
{
    /**
     * @return string The provider's name, used to bind a provider to processors
     */
    public function getName(): string;

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return ScheduleInterface[]
     */
    public function provideSchedules(int $limit = null, int $offset = null): array;

    public function save(ScheduleInterface $schedule): void;
}
