<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Rpc\Result;

use Throwable;

class Failure implements FailureInterface
{
    public function __construct(
        private Throwable $failureReason,
    )
    {
    }

    public function getFailureReason(): ?Throwable
    {
        return $this->failureReason;
    }

    public function isSuccess(): bool
    {
        return false;
    }
}
