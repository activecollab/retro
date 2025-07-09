<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Queue\Timer;

use ActiveCollab\CurrentTimestamp\CurrentTimestampInterface;

class JobsConsumerTimer implements JobsConsumerTimerInterface
{
    public function __construct(
        private CurrentTimestampInterface $currentTimestamp,
        private float $startTime,
        private int $runtime,
    )
    {
    }

    public function getStartTime(): float
    {
        return $this->startTime;
    }

    public function getRuntime(): int
    {
        return $this->runtime;
    }

    public function getWorkUntil(): int
    {
        return $this->currentTimestamp->getCurrentTimestamp() + $this->runtime;
    }
}
