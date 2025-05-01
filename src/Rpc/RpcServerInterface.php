<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Rpc;

use ActiveCollab\Retro\Rpc\Result\Json\JsonRpcResultInterface;
use ActiveCollab\Retro\Rpc\Result\ResultInterface;

interface RpcServerInterface
{
    public function run(
        string $bundleName,
        string $serviceName,
        string $methodName,
        array $params = [],
    ): ResultInterface;
    public function json(string $payload): JsonRpcResultInterface;
    public function registerService(string $bundleClass, string ...$serviceClasses): void;
    public function hasMethod(
        string $bundleClass,
        string $serviceClass,
        string $methodName,
    ): bool;
}
