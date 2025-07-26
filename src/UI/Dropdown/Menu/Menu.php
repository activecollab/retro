<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Dropdown\Menu;

use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuElementInterface;
use ActiveCollab\Retro\UI\Dropdown\Common\PanelInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Menu implements MenuInterface
{
    /**
     * @var MenuElementInterface[]
     */
    private array $elements;

    public function __construct(
        MenuElementInterface ...$elements
    )
    {
        $this->elements = $elements;
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderMenu($this, ...$extensions);
    }
}
