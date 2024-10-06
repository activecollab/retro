<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\Metadata;

class AppAppNamespace implements AppNamespaceInterface
{
    public function __construct(private string $appNamespace)
    {
    }

    public function getNamespace(): string
    {
        return $this->appNamespace;
    }
}
