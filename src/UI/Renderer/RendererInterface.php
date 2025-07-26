<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Renderer;

use ActiveCollab\Retro\UI\Dropdown\Button\ButtonInterface;
use ActiveCollab\Retro\UI\Dropdown\DropdownInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuElementInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\MenuInterface;

interface RendererInterface
{
    public function renderButton(
        ButtonInterface $button,
        RenderingExtensionInterface ...$extensions,
    ): string;

    public function renderDropdown(
        DropdownInterface $dropdown,
        RenderingExtensionInterface ...$extensions,
    ): string;

    public function renderMenu(
        MenuInterface $menu,
        RenderingExtensionInterface ...$extensions,
    ): string;
    public function renderMenuItem(
        MenuElementInterface $menuElement,
        RenderingExtensionInterface ...$extensions,
    ): string;
}
