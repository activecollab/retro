<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Indicator;

use ActiveCollab\Retro\UI\Common\Property\Trait\WithLabelTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithTooltipTrait;
use ActiveCollab\Retro\UI\Indicator\Tooltip\TooltipInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Icon implements IconInterface
{
    use WithLabelTrait;
    use WithTooltipTrait;

    public function __construct(
        private string $iconName,
        ?string $label = null,
        ?TooltipInterface $tooltip = null,
    )
    {
        $this->label = $label;
        $this->tooltip = $tooltip;
    }

    public function getIconName(): string
    {
        return $this->iconName;
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
        return $renderer->renderIcon($this, ...$extensions);
    }
}
