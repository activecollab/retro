<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\Bundle;

abstract class Bundle implements BundleInterface
{
    private ?string $name = null;

    public function getName(): string
    {
        if ($this->name === null) {
            $bits = explode('\\', $this::class);

            $this->name = $this->removeBundleSuffix($bits[count($bits) - 1]);
        }

        return $this->name;
    }

    public function getRpcServices(): array
    {
        return [];
    }

    private function removeBundleSuffix(string $bundleName): string
    {
        if (str_ends_with($bundleName, 'Bundle')) {
            return substr($bundleName, 0, strlen($bundleName) - 6);
        }

        return $bundleName;
    }
}
