<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Renderer\Shoelace;

use ActiveCollab\Retro\TemplatedUI\ComponentIdResolver\ComponentIdResolverInterface;
use ActiveCollab\Retro\TemplatedUI\Property\ButtonVariant;
use ActiveCollab\Retro\UI\Action\ActionInterface;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Indicator\BadgeInterface;
use ActiveCollab\Retro\UI\Button\ButtonInterface;
use ActiveCollab\Retro\UI\Dropdown\DropdownInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\Divider;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\Label;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuElementInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuItem\MenuItem;
use ActiveCollab\Retro\UI\Dropdown\Menu\MenuInterface;
use ActiveCollab\Retro\UI\Indicator\IconInterface;
use ActiveCollab\Retro\UI\Navigation\Tab\TabGroupInterface;
use ActiveCollab\Retro\UI\Navigation\Tab\TabInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;
use ActiveCollab\Retro\UI\Renderer\Shoelace\Extension\Slot;
use ActiveCollab\TemplatedUI\Helper\HtmlHelpersTrait;
use LogicException;

class ShoelaceRenderer implements RendererInterface
{
    use HtmlHelpersTrait;

    public function __construct(
        private ComponentIdResolverInterface $componentIdResolver,
    )
    {
    }

    public function renderPreRenderedContent(
        PreRenderedElementInterface $preRenderedElement,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $preRenderedElement->getPreRenderedContent();
    }

    public function renderButton(
        ButtonInterface $button,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        $attributes = [
            'type' => $button->getType() ?? 'button',
            'variant' => $button->getVariant()
                ? $button->getVariant()->toAttributeValue()
                : ButtonVariant::PRIMARY->toAttributeValue(),
        ];

        if ($button->getSize()) {
            $attributes['size'] = $button->getSize()->toAttributeValue();
        }

        if ($button->getStyle()) {
            $attributes[$button->getStyle()->toAttributeName()] = true;
        }

        $attributes = $this->extendAttributes($attributes, $button->getAction(), ...$extensions);

        return sprintf(
            '%s%s%s%s%s',
            $this->openHtmlTag('sl-button', $attributes),
            $this->renderButtonContent($button),
            $button->getLeftAdornment()?->renderUsingRenderer($this, new Slot('prefix')) ?? '',
            $button->getRightAdornment()?->renderUsingRenderer($this, new Slot('suffix')) ?? '',
            $this->closeHtmlTag('sl-button'),
        );
    }

    private function renderButtonContent(ButtonInterface $button): string
    {
        if ($button->getContent() instanceof IconInterface) {
            return $this->renderIcon($button->getContent());
        }

        if ($button->getContent() instanceof PreRenderedElementInterface) {
            return $button->getContent()->getPreRenderedContent();
        }

        return $this->sanitizeForHtml($button->getContent());
    }

    public function renderIcon(
        IconInterface $icon,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        $attributes = [
            'name' => $icon->getIconName(),
        ];

        if ($icon->getLabel()) {
            $attributes['label'] = $icon->getLabel();
        }

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

    public function renderTabGroup(
        TabGroupInterface $tabGroup,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        $attributes = $this->extendAttributes(
            null,
            null,
            ...$extensions,
        );

        return sprintf(
            '%s%s%s',
            $this->openHtmlTag('sl-tab-group', $attributes),
            $this->renderTabGroupContent($tabGroup),
            $this->closeHtmlTag('sl-tab-group'),
        );
    }

    private function renderTabGroupContent(TabGroupInterface $tabGroup): string
    {
        if ($tabGroup->getPreRenderedElement()) {
            return $tabGroup->getPreRenderedElement()->renderUsingRenderer($this);
        }

        return implode(
            '',
            array_map(
                fn (TabInterface $tab): string => $tab->renderUsingRenderer($this, new Slot('nav')),
                $tabGroup->getTabs(),
            ),
        );
    }

    public function renderTab(
        TabInterface $tab,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        $tabName = $tab->getName();

        if (empty($tabName)) {
            $tabName = $this->componentIdResolver->getUniqueId('tab');
        }

        $attributes = $this->extendAttributes(
            [
                'panel' => $tabName,
            ],
            null,
            ...$extensions,
        );

        return sprintf(
            '%s%s%s%s%s%s',
            $this->openHtmlTag('sl-tab', $attributes),
            $tab->getLabel(),
            $this->closeHtmlTag('sl-tab'),
            $this->openHtmlTag(
                'sl-tab-panel',
                [
                    'name' => $tabName,
                ],
            ),
            $tab->getContent(),
            $this->closeHtmlTag('sl-tab-panel'),
        );
    }


    private function extendAttributes(
        ?array $attributes = null,
        ?ActionInterface $action = null,
        RenderingExtensionInterface ...$extensions,
    ): array
    {
        if ($attributes === null) {
            $attributes = [];
        }

        if ($action) {
            $attributes = $action->extendAttributes($attributes);
        }

        foreach ($extensions as $extension) {
            $attributes = $extension->extendAttributes($attributes);
        }

        return $attributes;
    }
}
