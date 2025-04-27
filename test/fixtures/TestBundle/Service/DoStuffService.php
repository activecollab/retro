<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\Fixtures\TestBundle\Service;

use ActiveCollab\Retro\Rpc\Attribute\RpcMethod;
use ActiveCollab\Retro\Service\Service;

class DoStuffService extends Service
{
    #[RpcMethod]
    public function sumTwoNumbers(int $first, int $second): int
    {
        return $first + $second;
    }

    #[RpcMethod]
    public function recordClick(): void
    {
    }
}
