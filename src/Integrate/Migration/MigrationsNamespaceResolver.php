<?php

/*
 * This file is part of the HoneySuper project.
 *
 * (c) ActiveCollab, Inc <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Integrate\Migration;

class MigrationsNamespaceResolver implements MigrationsNamespaceResolverInterface
{
    public function __construct(
        private string $migrationsNamespace,
    )
    {
    }

    public function getMigrationsNamespace(): string
    {
        return $this->migrationsNamespace;
    }
}
