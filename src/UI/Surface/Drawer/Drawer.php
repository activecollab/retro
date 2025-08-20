<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Surface\Drawer;

use ActiveCollab\Retro\UI\Common\Property\Trait\WithIdTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithRequiredLabelTrait;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Drawer implements DrawerInterface
{
    use WithRequiredLabelTrait;
    use WithIdTrait;

    public function __construct(
        string $label,
        private PreRenderedElementInterface|string $content,
    )
    {
        $this->label = $label;
    }

    public function getContent(): PreRenderedElementInterface|string
    {
        return $this->content;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderDrawer($this, ...$extensions);
    }
}
