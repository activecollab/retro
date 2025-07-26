<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Dropdown;

use ActiveCollab\Retro\UI\Common\PanelInterface;
use ActiveCollab\Retro\UI\Common\TriggerInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Dropdown implements DropdownInterface
{
    public function __construct(
        private TriggerInterface|RenderableElementInterface $trigger,
        private PanelInterface|RenderableElementInterface $panel,
    )
    {
    }

    public function getTrigger(): TriggerInterface|RenderableElementInterface
    {
        return $this->trigger;
    }

    public function getPanel(): PanelInterface|RenderableElementInterface
    {
        return $this->panel;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderDropdown($this, ...$extensions);
    }
}
