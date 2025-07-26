<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Renderer;

interface RenderingExtensionInterface
{
    public function extendAttributes(array $attributes): array;
}
