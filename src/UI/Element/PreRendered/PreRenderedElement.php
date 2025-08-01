<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Element\PreRendered;

use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class PreRenderedElement implements PreRenderedElementInterface
{
    public function __construct(
        private string $preRenderedContent,
    )
    {
    }

    public function getPreRenderedContent(): string
    {
        return $this->preRenderedContent;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderPreRenderedContent($this, ...$extensions);
    }
}
