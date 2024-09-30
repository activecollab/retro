<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\Metadata;

interface MetadataInterface
{
    public function getName(): string;
    public function getVersion(): string;
    public function getPath(): string;
    public function getUrl(): string;
}
