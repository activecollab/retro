<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Rpc;

use ActiveCollab\Retro\Service\ServiceInterface;

interface ServiceResolverInterface
{
    public function getService(string $bundleClass, string $serviceClass): ?ServiceInterface;
}
