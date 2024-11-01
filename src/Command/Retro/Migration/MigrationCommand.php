<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro\Migration;

use ActiveCollab\DatabaseMigrations\MigrationsInterface;
use ActiveCollab\Retro\Command\Retro\RetroCommand;
use RuntimeException;

abstract class MigrationCommand extends RetroCommand
{
    public function getCommandNamePrefix(): string
    {
        return parent::getCommandNamePrefix() . 'migration:';
    }

    protected function getMigrations(): MigrationsInterface
    {
        $migrations = $this->get(MigrationsInterface::class);

        if ($migrations instanceof MigrationsInterface) {
            return $migrations;
        }

        throw new RuntimeException('Failed to get migrations utility from DI container');
    }
}