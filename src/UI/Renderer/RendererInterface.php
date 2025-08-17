<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Renderer;

use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Form\Radio\RadioGroupInterface;
use ActiveCollab\Retro\UI\Indicator\BadgeInterface;
use ActiveCollab\Retro\UI\Button\ButtonInterface;
use ActiveCollab\Retro\UI\Dropdown\DropdownInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuElementInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\MenuInterface;
use ActiveCollab\Retro\UI\Indicator\IconInterface;
use ActiveCollab\Retro\UI\Indicator\InfoBoxInterface;
use ActiveCollab\Retro\UI\Indicator\Tooltip\TooltipInterface;
use ActiveCollab\Retro\UI\Navigation\Tab\TabGroupInterface;
use ActiveCollab\Retro\UI\Navigation\Tab\TabInterface;

interface RendererInterface
{
    public function renderPreRenderedContent(
        PreRenderedElementInterface $preRenderedElement,
        RenderingExtensionInterface ...$extensions,
    ): string;

    public function renderTooltip(
        TooltipInterface $tooltip,
        RenderingExtensionInterface ...$extensions,
    ): string;

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

    public function renderIcon(
        IconInterface $icon,
        RenderingExtensionInterface ...$extensions,
    ): string;

    public function renderBadge(
        BadgeInterface $badge,
        RenderingExtensionInterface ...$extensions,
    ): string;

    public function renderInfoBox(
        InfoBoxInterface $infoBox,
        RenderingExtensionInterface ...$extensions,
    ): string;

    public function renderTabGroup(
        TabGroupInterface $tabGroup,
        RenderingExtensionInterface ...$extensions,
    ): string;

    public function renderTab(
        TabInterface $tab,
        RenderingExtensionInterface ...$extensions,
    ): string;

    public function renderRadioGroup(
        RadioGroupInterface $radioGroup,
        RenderingExtensionInterface ...$extensions,
    ): string;
}
