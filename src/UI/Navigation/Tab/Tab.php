<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Navigation\Tab;

use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Tab implements TabInterface
{
    public function __construct(
        private string $label,
        private string $content,
        private ?string $name = null,
    )
    {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderTab($this, ...$extensions);
    }
}
