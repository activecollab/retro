<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Indicator;

use ActiveCollab\Retro\UI\Common\Variant;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class InfoBox implements InfoBoxInterface
{
    public function __construct(
        private PreRenderedElement|string $content,
        private Variant $variant = Variant::PRIMARY,
        private ?IconInterface $icon = null,
    )
    {
    }

    public function getContent(): PreRenderedElement|string
    {
        return $this->content;
    }

    public function getVariant(): Variant
    {
        return $this->variant;
    }

    public function getIcon(): ?IconInterface
    {
        return $this->icon;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderInfoBox($this, ...$extensions);
    }
}
