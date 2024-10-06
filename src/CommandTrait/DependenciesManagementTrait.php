<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\CommandTrait;

use ActiveCollab\Retro\Bootstrapper\Bundle\BundleInterface;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

trait DependenciesManagementTrait
{
    private function autowire(
        BundleInterface $bundle,
        string $dependencyFqn,
        string $dependencyImplementationFqn,
        OutputInterface $output,
    ): void
    {
        $dependencyName = $this->getDependencyNameFromFqn($dependencyFqn);
        $dependencyImplementationName = $this->getDependencyNameFromFqn($dependencyImplementationFqn);

        if ($this->getContainer()->has(ltrim($dependencyFqn, '\\'))) {
            $output->writeln(
                sprintf(
                    'Service <comment>%s</comment> already exists in DI container. Skipping...',
                    $dependencyName,
                ),
            );

            return;
        }

        $dependenciesFile = $bundle::DEPENDENCIES;

        if (!is_file($dependenciesFile)) {
            $output->writeln(
                sprintf(
                    'Dependencies file <comment>%s</comment> does not exist. Skipping...',
                    $dependenciesFile,
                ),
            );

            return;
        }

        $lines = file($dependenciesFile);

        $lastUseLine = $this->getLastUseLineInDependenciesFile($lines);

        array_splice(
            $lines,
            $lastUseLine + 1,
            0,
            [
                sprintf("use %s;\n", ltrim($dependencyImplementationFqn, '\\')),
                sprintf("use %s;\n", ltrim($dependencyFqn, '\\')),
            ],
        );

        $closeArrayLine = 0;

        foreach ($lines as $k => $line) {
            if (str_starts_with($line, '];')) {
                $closeArrayLine = $k;
            }
        }

        if (empty($closeArrayLine)) {
            throw new RuntimeException('Could not find closing array line in dependencies file.');
        }

        array_splice(
            $lines,
            $closeArrayLine,
            0,
            [
                sprintf(
                    "    %s::class => get(%s::class),\n",
                    $dependencyName,
                    $dependencyImplementationName,
                ),
            ],
        );

        file_put_contents($bundle::DEPENDENCIES, implode('', $lines));
        $output->writeln(
            sprintf(
                'Service <comment>%s</comment> added to dependencies.php file of <comment>%s</comment> bundle.',
                $dependencyName,
                $bundle->getName(),
            ),
        );
    }

    private function getDependencyNameFromFqn(string $fqn): string
    {
        return substr($fqn, strrpos($fqn, '\\') + 1);
    }

    private function getLastUseLineInDependenciesFile(array $lines): int
    {
        $lastUseLine = 0;

        foreach ($lines as $k => $line) {
            if (str_starts_with($line, 'use ')) {
                $lastUseLine = $k;
            }
        }

        if ($lastUseLine > 0) {
            return $lastUseLine;
        }

        foreach ($lines as $k => $line) {
            if (trim($line) === 'declare(strict_types=1);') {
                return $k;
            }
        }

        return 0;
    }
}
