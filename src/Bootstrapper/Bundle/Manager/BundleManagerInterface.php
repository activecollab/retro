<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\Bundle\Manager;

use ActiveCollab\Retro\Bootstrapper\Bundle\BundleInterface;

interface BundleManagerInterface
{
    /**
     * @return BundleInterface[]
     */
    public function getBundles(): iterable;
    public function getByClassName(string $bundleClassName): BundleInterface;
    public function getByName(string $bundleName): BundleInterface;
}
