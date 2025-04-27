<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\Bundle;

interface BundleInterface
{
    const PATH = __DIR__;
    const DEPENDENCIES = __DIR__ . '/dependencies.php';

    public function getName(): string;
    public function getRpcServices(): array;
}
