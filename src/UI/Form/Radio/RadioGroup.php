<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Form\Radio;

use ActiveCollab\Retro\UI\Common\Property\Trait\WithExplainerTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithRequiredLabelTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithRequiredNameTrait;
use ActiveCollab\Retro\UI\Common\Size;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class RadioGroup implements RadioGroupInterface
{
    use WithRequiredLabelTrait;
    use WithRequiredNameTrait;
    use WithExplainerTrait;

    private array $options = [];

    public function __construct(
        string $name,
        string $label,
        private mixed $value,
        private ?Size $size = null,
        RadioInterfaceRequired ...$options,
    )
    {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getSize(): ?Size
    {
        return $this->size;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderRadioGroup($this, ...$extensions);
    }
}
