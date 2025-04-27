<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Rpc;

interface RpcServerInterface
{
    public function json(string $payload): mixed;
    public function registerService(string $bundleClass, string ...$serviceClasses): void;
    public function hasMethod(
        string $bundleClass,
        string $serviceClass,
        string $methodName,
    ): bool;
}
