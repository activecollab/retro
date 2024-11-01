<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro;

use ActiveCollab\DatabaseMigrations\Command\Create as CreateMigrationsHelper;
use ActiveCollab\DatabaseMigrations\MigrationsInterface;
use ActiveCollab\Retro\Integrate\Migration\MigrationsHeaderCommentResolverInterface;
use ActiveCollab\Retro\Integrate\Migration\MigrationsNamespaceResolverInterface;
use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class CreateMigrationCommand extends RetroCommand
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

    protected function getHeaderComment(): string
    {
        return $this->getContainer()
            ->get(MigrationsHeaderCommentResolverInterface::class)
                ->getMigrationsHeaderComment();
    }

    protected function getNamespace(): string
    {
        return $this->getContainer()
            ->get(MigrationsNamespaceResolverInterface::class)
                ->getMigrationsNamespace();
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

    protected function getMigrations(): MigrationsInterface
    {
        $migrations = $this->getContainer()->get(MigrationsInterface::class);

        if ($migrations instanceof MigrationsInterface) {
            return $migrations;
        }

        throw new RuntimeException('Failed to get migrations utility from DI container');
    }
}
