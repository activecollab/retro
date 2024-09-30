<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\AppBootstrapper\Cli;

use ActiveCollab\ClassFinder\ClassFinder;
use ActiveCollab\ContainerAccess\ContainerAccessInterface;
use ActiveCollab\Retro\Bootstrapper\AppBootstrapper\AppBootstrapper;
use ActiveCollab\Retro\Bootstrapper\AppBootstrapper\AppBootstrapperInterface;
use ActiveCollab\Retro\Command\CommandInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class SymfonyConsoleAppBootstrapper extends AppBootstrapper implements CliAppBootstrapperInterface
{
    private Application $app;
    private int $exit_code = 0;

    public function bootstrap(): AppBootstrapperInterface
    {
        parent::bootstrap();

        $this->beforeAppConstruction();
        $this->app = new Application(
            $this->getAppMetadata()->getName(),
            $this->getAppMetadata()->getVersion(),
        );
        $this->afterAppConstruction();

        $this->setIsBootstrapped();

        $this->scanDirsForCommands($this->app, $this->getContainer());

        return $this;
    }

    public function run(bool $silent = false): AppBootstrapperInterface
    {
        parent::run($silent);

        $this->exit_code = $this->app->run();
        $this->setIsRan();

        return $this;
    }

    public function getCommand(string $commandName): CommandInterface
    {
        $command = $this->app->find($commandName);

        if ($command instanceof CommandInterface) {
            return $command;
        }

        throw new \InvalidArgumentException("Command '$commandName' not found.");
    }

    /**
     * @param Command|CommandInterface $command
     */
    public function addCommand(CommandInterface $command): CliAppBootstrapperInterface
    {
        if (!$this->isBootstrapped()) {
            throw new \LogicException('App needs to be bootstrapped before we can add commands to it.');
        }

        $this->app->add($command);

        return $this;
    }

    protected function scanDirsForCommands(Application $app, ContainerInterface $container)
    {
        (new ClassFinder())->scanDirsForInstances(
            $this->getDirsToScan(),
            function (Command $command) use (&$app, &$container) {
                if ($command instanceof ContainerAccessInterface) {
                    $command->setContainer($container);
                }

                $this->addCommand($command);
            },
        );
    }

    protected function getDirsToScan(): array
    {
        return [];
    }
}
