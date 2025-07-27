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
use ActiveCollab\Retro\TemplatedUI\Property\Size;
use ActiveCollab\Retro\TemplatedUI\Property\Width;
use ActiveCollab\Retro\UI\Action\ActionInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Button implements ButtonInterface
{
    public function __construct(
        private string $content,
        private ?ActionInterface $action = null,
        private ?string $type = null,
        private ?ButtonVariant $variant = null,
        private ?ButtonStyle $style = null,
        private ?Size $size = null,
        private ?Width $width = null,
    )
    {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAction(): ?ActionInterface
    {
        return $this->action;
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

    public function getSize(): ?Size
    {
        return $this->size;
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
