<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\Metadata;

class Path implements PathInterface
{
    public function __construct(
        private string $path,
    )
    {
    }

    public function getPath(): string
    {
        return $this->path;
    }
}