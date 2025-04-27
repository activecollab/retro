<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Rpc\Result;

class Success implements SuccessInterface
{
    public function __construct(
        private mixed $result,
    )
    {
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function isSuccess(): bool
    {
        return true;
    }
}
