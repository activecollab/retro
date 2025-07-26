<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Renderer\Shoelace\Extension;

use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Slot implements RenderingExtensionInterface
{
    public function __construct(
        private string $name,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function extendAttributes(array $attributes): array
    {
        return array_merge(
            $attributes,
            [
                'slot' => $this->name,
            ],
        );
    }
}
