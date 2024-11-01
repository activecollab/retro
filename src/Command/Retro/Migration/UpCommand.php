<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro\Migration;

use ActiveCollab\DatabaseMigrations\Command\Up as UpMigrationsHelper;

class UpCommand extends MigrationCommand
{
    use UpMigrationsHelper;

    protected function configure()
    {
        parent::configure();

        $this->setDescription('Run all migrations that are not executed');
    }
}
