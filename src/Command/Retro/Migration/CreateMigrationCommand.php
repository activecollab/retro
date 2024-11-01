<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro\Migration;

use ActiveCollab\DatabaseMigrations\Command\Create as CreateMigrationsHelper;
use ActiveCollab\DatabaseMigrations\MigrationsInterface;
use ActiveCollab\Retro\Command\Retro\RetroCommand;
use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class CreateMigrationCommand extends MigrationCommand
{
    use CreateMigrationsHelper;

    public function configure()
    {
        parent::configure();

        $this
            ->setDescription('Create a new migration')
            ->addArgument(
                'migration_name',
                InputArgument::REQUIRED,
                'What is this migration supposed to do (use imperative voice)'
            )
            ->addOption(
                'changeset',
                '',
                InputOption::VALUE_REQUIRED, 'Changeset name'
            )
            ->addOption(
                'dry-run',
                '',
                InputOption::VALUE_NONE,
                "Example what you'll do, without creating an actual file"
            );
    }

    public function getMigrationName(InputInterface $input): string
    {
        return trim($input->getArgument('migration_name'));
    }

    protected function getExtraArguments(InputInterface $input): array
    {
        $result = [];

        if (!empty($input->getOption('changeset'))) {
            $result[] = $input->getOption('changeset');
        }

        return $result;
    }
}
