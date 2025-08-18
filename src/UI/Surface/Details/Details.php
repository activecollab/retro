<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Surface\Details;

use ActiveCollab\Retro\UI\Common\Property\Trait\WithDisabledTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithRequiredLabelTrait;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Details implements DetailsInterface
{
    use WithDisabledTrait;
    use WithRequiredLabelTrait;

    public function __construct(
        string $label,
        private PreRenderedElementInterface|string $content,
        private bool $open = true,
        ?bool $disabled = null,
    )
    {
        $this->label = $label;
        $this->disabled = $disabled;
    }

    public function getContent(): PreRenderedElementInterface|string
    {
        return $this->content;
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderDetails($this, ...$extensions);
    }
}
