<?php

namespace Abc\Scheduler;

class NullProvider implements ProviderInterface
{
    public function getName(): string
    {
        return 'null';
    }

    public function provideSchedules(int $limit = null, int $offset = null): array
    {
        return [];
    }

    public function save(ScheduleInterface $schedule): void
    {
    }
}
