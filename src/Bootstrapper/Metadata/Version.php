<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\Metadata;

class Version implements VersionInterface
{
    public function __construct(
        private string $version,
    )
    {
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
