<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Queue\Timer;

interface JobsConsumerTimerInterface
{
    public function getStartTime(): float;
    public function getRuntime(): int;
    public function getWorkUntil(): int;
}
