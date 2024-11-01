<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro\Migration;

use ActiveCollab\DatabaseMigrations\Command\All as AllMigrationsHelper;
use ActiveCollab\Retro\Command\Retro\RetroCommand;

class ListCommand extends MigrationCommand
{
    use AllMigrationsHelper;

    protected function configure()
    {
        parent::configure();

        $this->setDescription('List all migrations');
    }
}
