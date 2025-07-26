<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Renderer\Shoelace;

use ActiveCollab\Retro\UI\Indicator\BadgeInterface;
use ActiveCollab\Retro\UI\Button\ButtonInterface;
use ActiveCollab\Retro\UI\Dropdown\DropdownInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\Divider;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\Label;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuElementInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuItem\MenuItem;
use ActiveCollab\Retro\UI\Dropdown\Menu\MenuInterface;
use ActiveCollab\Retro\UI\Indicator\IconInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;
use ActiveCollab\Retro\UI\Renderer\Shoelace\Extension\Slot;
use ActiveCollab\TemplatedUI\Helper\HtmlHelpersTrait;
use LogicException;

class ShoelaceRenderer implements RendererInterface
{
    use HtmlHelpersTrait;

    public function renderButton(
        ButtonInterface $button,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        $attributes = [];

        foreach ($extensions as $extension) {
            $attributes = $extension->extendAttributes($attributes);
        }

        return sprintf(
            '%s%s%s',
            $this->openHtmlTag('sl-button', $attributes),
            $this->sanitizeForHtml($button->getLabel()),
            $this->closeHtmlTag('sl-button'),
        );
    }

    public function renderIcon(
        IconInterface $icon,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        $attributes = [
            'name' => $icon->getIconName(),
        ];

        foreach ($extensions as $extension) {
            $attributes = $extension->extendAttributes($attributes);
        }

        return sprintf(
            '%s%s',
            $this->openHtmlTag('sl-icon', $attributes),
            $this->closeHtmlTag('sl-icon'),
        );
    }

    public function renderBadge(
        BadgeInterface $badge,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        $attributes = [];

        foreach ($extensions as $extension) {
            $attributes = $extension->extendAttributes($attributes);
        }

        return sprintf(
            '%s%s%s',
            $this->openHtmlTag('sl-badge', $attributes),
            $this->sanitizeForHtml($badge->getValue()),
            $this->closeHtmlTag('sl-badge'),
        );
    }

    public function renderDropdown(
        DropdownInterface $dropdown,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return sprintf(
            '%s%s%s%s',
            $this->openHtmlTag('sl-dropdown'),
            $dropdown->getTrigger()->renderUsingRenderer($this, new Slot('trigger')),
            $dropdown->getPanel()->renderUsingRenderer($this),
            $this->closeHtmlTag('sl-dropdown'),
        );
    }

    public function renderMenu(
        MenuInterface $menu,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return sprintf(
            '%s%s%s',
            $this->openHtmlTag('sl-menu'),
            implode(
                '',
                array_map(
                    fn (MenuElementInterface $element): string => $element->renderUsingRenderer($this),
                    $menu->getElements(),
                ),
            ),
            $this->closeHtmlTag('sl-menu'),
        );
    }

    public function renderMenuItem(
        MenuElementInterface $menuElement,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        if ($menuElement instanceof MenuItem) {
            $attributes = [];

            if ($menuElement->getAction()) {
                $attributes = $menuElement->getAction()->extendAttributes($attributes);
            }

            foreach ($extensions as $extension) {
                $attributes = $extension->extendAttributes($attributes);
            }

            return sprintf(
                '%s%s%s%s%s',
                $this->openHtmlTag('sl-menu-item', $attributes),
                $this->sanitizeForHtml($menuElement->getLabel()),
                $menuElement->getLeftAdornment()?->renderUsingRenderer($this, new Slot('prefix')) ?? '',
                $menuElement->getRightAdornment()?->renderUsingRenderer($this, new Slot('suffix')) ?? '',
                $this->closeHtmlTag('sl-menu-item'),
            );
        }

        if ($menuElement instanceof Divider) {
            return '<sl-divider></sl-divider>';
        }

        if ($menuElement instanceof Label) {
            return sprintf(
                '%s%s%s',
                $this->openHtmlTag('sl-menu-label'),
                $this->sanitizeForHtml($menuElement->getLabel()),
                $this->closeHtmlTag('sl-menu-label'),
            );
        }

        throw new LogicException(sprintf('Unknown menu element type: %s', $menuElement::class));
    }
}
