<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Navigation\Tab;

use ActiveCollab\Retro\UI\Common\Property\Trait\WithNameTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithRequiredLabelTrait;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Tab implements TabInterfaceRequired
{
    use WithNameTrait;
    use WithRequiredLabelTrait;

    public function __construct(
        string $label,
        private string $content,
        ?string $name = null,
    )
    {
        $this->label = $label;
        $this->name = $name;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderTab($this, ...$extensions);
    }
}
