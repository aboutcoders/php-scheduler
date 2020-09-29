<?php

namespace Abc\Scheduler;

use MyCLabs\Enum\Enum;

/**
 * @method static ConcurrencyPolicy ALLOW()
 * @method static ConcurrencyPolicy FORBID()
 */
class ConcurrencyPolicy extends Enum
{
    private const ALLOW = 'Allow';
    private const FORBID = 'Forbid';
}
