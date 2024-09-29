<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\Bundle\Manager;

use ActiveCollab\Retro\Bootstrapper\Bundle\BundleInterface;
use LogicException;

class BundleManager implements BundleManagerInterface
{
    private array $bundleClasses;
    private array $bundles = [];
    private array $bundleClassesByName = [];

    public function __construct(
        string ...$bundleClasses
    )
    {
        $this->bundleClasses = $bundleClasses;

        foreach ($this->bundleClasses as $bundleClass) {
            $bundle = new $bundleClass();

            if (!$bundle instanceof BundleInterface) {
                throw new LogicException(sprintf('Invalid bundle class "%s".', $bundleClass));
            }

            $this->bundles[$bundle::class] = $bundle;
            $this->bundleClassesByName[$bundle->getName()] = $bundle::class;
        }
    }

    public function getBundles(): iterable
    {
        return array_values($this->bundles);
    }

    public function getByClassName(string $bundleClassName): BundleInterface
    {
        if (empty($this->bundles[$bundleClassName])) {
            throw new LogicException(sprintf('Bundle for class "%s" not found.', $bundleClassName));
        }

        return $this->bundles[$bundleClassName];
    }

    public function getByName(string $bundleName): BundleInterface
    {
        return $this->getByClassName($this->getClassNameByBundleName($bundleName));
    }

    private function getClassNameByBundleName(string $bundleName): string
    {
        if (empty($this->bundleClassesByName[$bundleName])) {
            throw new LogicException(sprintf('Bundle "%s" not found.', $bundleName));
        }

        return $this->bundleClassesByName[$bundleName];
    }
}
