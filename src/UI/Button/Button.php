<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Button;

use ActiveCollab\Retro\TemplatedUI\Property\ButtonStyle;
use ActiveCollab\Retro\TemplatedUI\Property\ButtonVariant;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithSizeTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithTooltipTrait;
use ActiveCollab\Retro\UI\Common\Size;
use ActiveCollab\Retro\TemplatedUI\Property\Width;
use ActiveCollab\Retro\UI\Action\ActionInterface;
use ActiveCollab\Retro\UI\Common\AdornmentInterface;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Indicator\IconInterface;
use ActiveCollab\Retro\UI\Indicator\Tooltip\TooltipInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Button implements ButtonInterface
{
    use WithTooltipTrait;
    use WithSizeTrait;

    public function __construct(
        private IconInterface|PreRenderedElementInterface|string $content,
        private ?ActionInterface $action = null,
        private ?AdornmentInterface $leftAdornment = null,
        private ?AdornmentInterface $rightAdornment = null,
        private ?string $type = null,
        private ?ButtonVariant $variant = null,
        private ?ButtonStyle $style = null,
        ?Size $size = null,
        private ?Width $width = null,
        ?TooltipInterface $tooltip = null,
    )
    {
        $this->size = $size;
        $this->tooltip = $tooltip;
    }

    public function getContent(): IconInterface|PreRenderedElementInterface|string
    {
        return $this->content;
    }

    public function getAction(): ?ActionInterface
    {
        return $this->action;
    }

    public function getLeftAdornment(): ?AdornmentInterface
    {
        return $this->leftAdornment;
    }

    public function getRightAdornment(): ?AdornmentInterface
    {
        return $this->rightAdornment;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getVariant(): ?ButtonVariant
    {
        return $this->variant;
    }

    public function getStyle(): ?ButtonStyle
    {
        return $this->style;
    }

    public function getWidth(): ?Width
    {
        return $this->width;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderButton($this, ...$extensions);
    }
}
