<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\AppBootstrapper\Cli;

use ActiveCollab\ClassFinder\ClassDir\ClassDir;
use ActiveCollab\ClassFinder\ClassFinder;
use ActiveCollab\ContainerAccess\ContainerAccessInterface;
use ActiveCollab\Retro\Bootstrapper\AppBootstrapper\AppBootstrapper;
use ActiveCollab\Retro\Bootstrapper\AppBootstrapper\AppBootstrapperInterface;
use ActiveCollab\Retro\Command\Command;
use ActiveCollab\Retro\Command\CommandInterface;
use InvalidArgumentException;
use LogicException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\Console\Application;

abstract class SymfonyConsoleAppBootstrapper extends AppBootstrapper implements CliAppBootstrapperInterface
{
    private Application $app;
    private int $exitCode = 0;

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

        $this->exitCode = $this->app->run();
        $this->setIsRan();

        return $this;
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    public function getCommand(string $commandName): CommandInterface
    {
        $command = $this->app->find($commandName);

        if ($command instanceof CommandInterface) {
            return $command;
        }

        throw new InvalidArgumentException(sprintf("Command '%s' not found.", $commandName));
    }

    public function addCommand(CommandInterface $command): CliAppBootstrapperInterface
    {
        if (!$this->isBootstrapped()) {
            throw new LogicException('App needs to be bootstrapped before we can add commands to it.');
        }

        $this->app->add($command);

        return $this;
    }

    protected function scanDirsForCommands(Application $app, ContainerInterface $container): void
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
        return [
            new ClassDir(
                sprintf(
                    dirname(__DIR__, 3) . '/Command',
                    $this->getAppMetadata()->getPath(),
                    $this->getAppMetadata()->getVersion(),
                ),
                (new ReflectionClass(Command::class))->getNamespaceName(),
                CommandInterface::class,
            ),
        ];
    }
}
