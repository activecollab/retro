<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\ContainerBuilder;

use ActiveCollab\Retro\Bootstrapper\Bundle\BundleInterface;
use DI\ContainerBuilder as BaseContainerBuilder;
use LogicException;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class ContainerBuilder implements ContainerBuilderInterface
{
    public function __construct(
        private string $appPath,
    )
    {
    }

    public function buildDefinitions(string $version, string ...$bundles): array
    {
        return array_merge(
            array_map(
                function (string $file) use ($version) {
                    return sprintf(
                        '%s/app/%s/%s',
                        $this->appPath,
                        $version,
                        $file,
                    );
                },
                $this->getAppDependencyFiles(),
            ),
            array_map(
                function (string $bundleClass) {
                    return $this->getBundleDependenciesFiles($bundleClass);
                },
                $bundles,
            ),
        );
    }

    public function buildContainer(string $version, string ...$bundles): ContainerInterface
    {
        $container_builder = new BaseContainerBuilder();

        $container_builder->addDefinitions(
            ...$this->buildDefinitions($version, ...$bundles),
        );

        return $container_builder->build();
    }

    protected function getAppDependencyFiles(): array
    {
        return [
            'settings.php',
            'bootstrap.php',
            'events.php',
            'database.php',
            'structure.php',
            'model.php',
            'routes.php',
            'auth.php',
            'utils.php',
        ];
    }

    private function getBundleDependenciesFiles(string $bundleClass): string
    {
        $bundleClassReflection = new ReflectionClass($bundleClass);

        if (!$bundleClassReflection->implementsInterface(BundleInterface::class)) {
            throw new LogicException('Invalid bundle class provided.');
        }

        return $bundleClassReflection->getConstant('DEPENDENCIES');
    }
}
