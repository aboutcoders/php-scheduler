<?php

namespace Abc\Scheduler\Extension;

use Abc\Scheduler\Context\Start;
use Abc\Scheduler\StartExtensionInterface;

class NicenessExtension implements StartExtensionInterface
{
    /**
     * @var int
     */
    protected $niceness = 0;

    /**
     * @param int $niceness
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($niceness)
    {
        if (false === is_int($niceness)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected niceness value is int but got: "%s"',
                is_object($niceness) ? get_class($niceness) : gettype($niceness)
            ));
        }

        $this->niceness = $niceness;
    }

    public function onStart(Start $context): void
    {
        if (0 !== $this->niceness) {
            $changed = @proc_nice($this->niceness);
            if (!$changed) {
                throw new \InvalidArgumentException(sprintf(
                    'Cannot change process niceness, got warning: "%s"',
                    error_get_last()['message']
                ));
            }
        }
    }
}
