<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Indicator;

use ActiveCollab\Retro\UI\Indicator\Tooltip\TooltipInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Badge implements BadgeInterface
{
    public function __construct(
        private string $value,
        private bool $rounded = false,
        private ?TooltipInterface $tooltip = null,
    )
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isRounded(): bool
    {
        return $this->rounded;
    }

    public function getTooltip(): ?TooltipInterface
    {
        return $this->tooltip;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderBadge($this, ...$extensions);
    }
}
