<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Form\Select\Element;

use ActiveCollab\Retro\UI\Common\Property\Trait\WithAdornmentsTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithRequiredLabelTrait;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Option implements OptionInterface
{
    use WithRequiredLabelTrait;
    use WithAdornmentsTrait;

    public function __construct(
        string $label,
        private mixed $value,
    )
    {
        $this->label = $label;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function labelIsAttribute(): bool
    {
        return false;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderSelectOption($this, ...$extensions);
    }
}
