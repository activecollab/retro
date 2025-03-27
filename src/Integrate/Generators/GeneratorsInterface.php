<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Integrate\Generators;

use ActiveCollab\Retro\Bootstrapper\Bundle\BundleInterface;

interface GeneratorsInterface
{
    public function getModelNamespace(): string;
    public function getBundlesNamespace(): string;
    public function getBundleNamespace(BundleInterface $bundle): string;
}
