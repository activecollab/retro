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
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;
use LogicException;

class RadioGroup implements RadioGroupInterface
{
    use WithRequiredNameTrait;
    use WithRequiredLabelTrait;
    use WithExplainerTrait;

    private ?PreRenderedElementInterface $preRenderedElement = null;
    private array $options = [];

    public function __construct(
        string $name,
        string $label,
        private mixed $value,
        private ?Size $size = null,
        PreRenderedElementInterface|RadioInterface $firstRadioOrPreRenderedElement,
        RadioInterface ...$additionalOptions,
    )
    {
        $this->name = $name;
        $this->label = $label;

        if ($firstRadioOrPreRenderedElement instanceof PreRenderedElementInterface) {
            $this->preRenderedElement = $firstRadioOrPreRenderedElement;

            if (!empty($additionalOptions)) {
                throw new LogicException('You cannot pass additional radios when the first argument is a pre-rendered element.');
            }
        } else {
            $this->options[] = $firstRadioOrPreRenderedElement;

            if (!empty($additionalOptions)) {
                $this->options = array_merge(
                    $this->options,
                    $additionalOptions,
                );
            }
        }
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
