<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\ContainerBuilder;

use Psr\Container\ContainerInterface;

interface ContainerBuilderInterface
{
    public function buildDefinitions(string $version, string ...$bundles): array;
    public function buildContainer(string $version, string ...$bundles): ContainerInterface;
}
