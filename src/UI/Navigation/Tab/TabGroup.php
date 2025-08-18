<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Navigation\Tab;

use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;
use LogicException;

class TabGroup implements TabGroupInterface
{
    private ?PreRenderedElementInterface $preRenderedElement = null;
    private array $tabs = [];

    public function __construct(
        PreRenderedElementInterface|TabInterfaceRequired $firstTabOrPreRenderedElement,
        TabInterfaceRequired ...$additionalTabs,
    )
    {
        if ($firstTabOrPreRenderedElement instanceof PreRenderedElementInterface) {
            $this->preRenderedElement = $firstTabOrPreRenderedElement;

            if (!empty($additionalTabs)) {
                throw new LogicException('You cannot pass additional tabs when the first argument is a pre-rendered element.');
            }
        } else {
            $this->tabs[] = $firstTabOrPreRenderedElement;

            if (!empty($additionalTabs)) {
                $this->tabs = array_merge(
                    $this->tabs,
                    $additionalTabs,
                );
            }
        }
    }

    public function getPreRenderedElement(): ?PreRenderedElementInterface
    {
        return $this->preRenderedElement;
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
