<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Form\Radio;

use ActiveCollab\Retro\UI\Common\Size;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class RadioGroup implements RadioGroupInterface
{
    public function __construct(
        private string $name,
        private string $label,
        private mixed $value,
        private ?Size $size = null,
        private ?string $explainer = null,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getSize(): ?Size
    {
        return $this->size;
    }

    public function getExplainer(): ?string
    {
        return $this->explainer;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderRadioGroup($this, ...$extensions);
    }
}
