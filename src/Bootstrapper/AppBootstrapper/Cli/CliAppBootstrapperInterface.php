<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\AppBootstrapper\Cli;

use ActiveCollab\Retro\Bootstrapper\AppBootstrapper\AppBootstrapperInterface;
use ActiveCollab\Retro\Command\CommandInterface;

interface CliAppBootstrapperInterface extends AppBootstrapperInterface
{
    public function getCommand(string $commandName): CommandInterface;
    public function addCommand(CommandInterface $command): CliAppBootstrapperInterface;
}
