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

class TabGroup implements TabGroupInterface
{
    private array $tabs;

    public function __construct(
        TabInterface ...$tabs,
    )
    {
        $this->tabs = $tabs;
    }

    public function getTabs(): array
    {
        return $this->tabs;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderTabGroup($this, ...$extensions);
    }
}
