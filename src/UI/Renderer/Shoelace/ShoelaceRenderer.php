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
use ActiveCollab\Retro\UI\Common\WithTooltipInterface;
use ActiveCollab\Retro\UI\Element\ElementInterface;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Form\Radio\RadioGroupInterface;
use ActiveCollab\Retro\UI\Indicator\BadgeInterface;
use ActiveCollab\Retro\UI\Button\ButtonInterface;
use ActiveCollab\Retro\UI\Dropdown\DropdownInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\Divider;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\Label;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuElementInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuItem\MenuItem;
use ActiveCollab\Retro\UI\Dropdown\Menu\MenuInterface;
use ActiveCollab\Retro\UI\Indicator\IconInterface;
use ActiveCollab\Retro\UI\Indicator\InfoBoxInterface;
use ActiveCollab\Retro\UI\Indicator\Tooltip\Tooltip;
use ActiveCollab\Retro\UI\Indicator\Tooltip\TooltipInterface;
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
        return $this->wrapOutput(
            $preRenderedElement->getPreRenderedContent(),
            $preRenderedElement,
        );
    }

    public function renderTooltip(
        TooltipInterface $tooltip,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        $attributes = $this->extendAttributes([], null, ...$extensions);

        return sprintf(
            '%s%s%s%s%s%s',
            $this->openHtmlTag('sl-tooltip', $attributes),
            $this->openHtmlTag('div', ['slot' => 'content']),
            $tooltip->getContent() instanceof PreRenderedElementInterface
                ? $tooltip->getContent()->getPreRenderedContent()
                : $this->sanitizeForHtml($tooltip->getContent()),
            $this->closeHtmlTag('div'),
            $tooltip->getWrapAround()
                ? $tooltip->getWrapAround()->renderUsingRenderer($this)
                : '',
            $this->closeHtmlTag('sl-tooltip'),
        );
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
            $attributes['size'] = $button->getSize()->value;
        }

        if ($button->getStyle()) {
            $attributes[$button->getStyle()->toAttributeName()] = true;
        }

        $attributes = $this->extendAttributes($attributes, $button->getAction(), ...$extensions);

        return $this->wrapOutput(
            sprintf(
                '%s%s%s%s%s',
                $this->openHtmlTag('sl-button', $attributes),
                $this->renderButtonContent($button),
                $button->getLeftAdornment()?->renderUsingRenderer($this, new Slot('prefix')) ?? '',
                $button->getRightAdornment()?->renderUsingRenderer($this, new Slot('suffix')) ?? '',
                $this->closeHtmlTag('sl-button'),
            ),
            $button,
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

        return $this->wrapOutput(
            sprintf(
                '%s%s',
                $this->openHtmlTag('sl-icon', $attributes),
                $this->closeHtmlTag('sl-icon'),
            ),
            $icon,
        );
    }

    public function renderBadge(
        BadgeInterface $badge,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        $attributes = $this->extendAttributes(
            [
                'pill' => $badge->isRounded(),
                'variant' => $badge->getVariant()->value,
            ],
            null,
            ...$extensions,
        );

        return $this->wrapOutput(
            sprintf(
                '%s%s%s',
                $this->openHtmlTag('sl-badge', $attributes),
                $this->sanitizeForHtml($badge->getValue()),
                $this->closeHtmlTag('sl-badge'),
            ),
            $badge,
        );
    }

    public function renderInfoBox(
        InfoBoxInterface $infoBox,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        $attributes = $this->extendAttributes(
            [
                'variant' => $infoBox->getVariant()->value,
                'open' => true,
            ],
            ...$extensions,
        );

        return $this->wrapOutput(
            sprintf(
                '%s%s%s%s',
                $this->openHtmlTag('sl-alert', $attributes),
                $this->renderInfoBoxIcon($infoBox),
                $infoBox->getContent() instanceof PreRenderedElementInterface
                    ? $infoBox->getContent()->getPreRenderedContent()
                    : $this->sanitizeForHtml($infoBox->getContent()),
                $this->closeHtmlTag('sl-alert'),
            ),
            $infoBox,
        );
    }

    private function renderInfoBoxIcon(InfoBoxInterface $infoBox): string
    {
        if (empty($infoBox->getIcon())) {
            return '';
        }

        return $this->renderIcon($infoBox->getIcon(), new Slot('icon'));
    }

    public function renderDropdown(
        DropdownInterface $dropdown,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $this->wrapOutput(
            sprintf(
                '%s%s%s%s',
                $this->openHtmlTag('sl-dropdown'),
                $dropdown->getTrigger()->renderUsingRenderer($this, new Slot('trigger')),
                $dropdown->getPanel()->renderUsingRenderer($this),
                $this->closeHtmlTag('sl-dropdown'),
            ),
            $dropdown,
        );
    }

    public function renderMenu(
        MenuInterface $menu,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $this->wrapOutput(
            sprintf(
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
            ),
            $menu,
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

            return $this->wrapOutput(
                sprintf(
                    '%s%s%s%s%s',
                    $this->openHtmlTag('sl-menu-item', $attributes),
                    $this->sanitizeForHtml($menuElement->getLabel()),
                    $menuElement->getLeftAdornment()?->renderUsingRenderer($this, new Slot('prefix')) ?? '',
                    $menuElement->getRightAdornment()?->renderUsingRenderer($this, new Slot('suffix')) ?? '',
                    $this->closeHtmlTag('sl-menu-item'),
                ),
                $menuElement,
            );
        }

        if ($menuElement instanceof Divider) {
            return $this->wrapOutput(
                '<sl-divider></sl-divider>',
                $menuElement,
            );
        }

        if ($menuElement instanceof Label) {
            return $this->wrapOutput(
                sprintf(
                    '%s%s%s',
                    $this->openHtmlTag('sl-menu-label'),
                    $this->sanitizeForHtml($menuElement->getLabel()),
                    $this->closeHtmlTag('sl-menu-label'),
                ),
                $menuElement,
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

        return $this->wrapOutput(
            sprintf(
                '%s%s%s',
                $this->openHtmlTag('sl-tab-group', $attributes),
                $this->renderTabGroupContent($tabGroup),
                $this->closeHtmlTag('sl-tab-group'),
            ),
            $tabGroup,
        );
    }

    private function renderTabGroupContent(TabGroupInterface $tabGroup): string
    {
        if ($tabGroup->getPreRenderedElement()) {
            return $tabGroup->getPreRenderedElement()->renderUsingRenderer($this);
        }

        return $this->wrapOutput(
            implode(
                '',
                array_map(
                    fn (TabInterface $tab): string => $tab->renderUsingRenderer($this, new Slot('nav')),
                    $tabGroup->getTabs(),
                ),
            ),
            $tabGroup,
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

        return $this->wrapOutput(
            sprintf(
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
            ),
            $tab,
        );
    }

    public function renderRadioGroup(
        RadioGroupInterface $radioGroup,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        $attributes = [
            'name' => $radioGroup->getName(),
            'label' => $radioGroup->getLabel(),
            'value' => $radioGroup->getValue(),
        ];

        if ($radioGroup->getSize()) {
            $attributes['size'] = $radioGroup->getSize()->value;
        }

        if ($radioGroup->getExplainer()) {
            $attributes['help-text'] = $radioGroup->getExplainer();
        }

        return $this->wrapOutput(
            sprintf(
                '%s%s',
                $this->openHtmlTag(
                    'sl-radio-group',
                    $this->extendAttributes(
                        $attributes,
                        null,
                        ...$extensions,
                    ),
                ),
                $this->closeHtmlTag('sl-radio-group'),
            ),
            $radioGroup,
        );
    }

    private function wrapOutput(
        string $output,
        ?ElementInterface $element,
    ): string
    {
        if ($element instanceof WithTooltipInterface && $element->getTooltip()) {
            return $this->renderTooltip(
                new Tooltip(
                    $element->getTooltip()->getContent(),
                    new PreRenderedElement($output),
                ),
            );
        }

        return $output;
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
