<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Indicator;

use ActiveCollab\Retro\UI\Common\Property\Trait\WithTooltipTrait;
use ActiveCollab\Retro\UI\Common\Variant;
use ActiveCollab\Retro\UI\Indicator\Tooltip\Tooltip;
use ActiveCollab\Retro\UI\Indicator\Tooltip\TooltipInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Badge implements BadgeInterface
{
    use WithTooltipTrait;

    public function __construct(
        private string $value,
        private Variant $variant = Variant::PRIMARY,
        private bool $rounded = false,
        ?TooltipInterface $tooltip = null,
    )
    {
        $this->tooltip = $tooltip;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getVariant(): Variant
    {
        return $this->variant;
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
