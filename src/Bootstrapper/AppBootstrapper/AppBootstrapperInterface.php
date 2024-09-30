<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\AppBootstrapper;

use ActiveCollab\Retro\Bootstrapper\Metadata\MetadataInterface;
use Psr\Container\ContainerInterface;

interface AppBootstrapperInterface
{
    public function getAppMetadata(): MetadataInterface;
    public function getContainer(): ContainerInterface;

    public function isBootstrapped(): bool;
    public function bootstrap(): AppBootstrapperInterface;

    public function isRan(): bool;
    public function run(bool $silent = false): AppBootstrapperInterface;
}
