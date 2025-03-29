<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Integrate\Creator;

use ActiveCollab\Retro\Bootstrapper\Bundle\BundleInterface;

interface CreatorInterface
{
    public function getModelNamespace(): string;
    public function getBundlesNamespace(): string;
    public function getBundleNamespace(BundleInterface $bundle): string;
    public function getBaseService(bool $interface = false): string;
    public function getServiceNamespace(BundleInterface $bundle): string;
    public function getDefaultServiceContext(): ?string;
    public function getBaseUser(bool $interface): string;
}
