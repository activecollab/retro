<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Form\Radio;

use ActiveCollab\Retro\UI\Common\Property\Trait\WithDisabledTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithRequiredLabelTrait;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;

class Radio implements RadioInterface
{
    use WithRequiredLabelTrait;
    use WithDisabledTrait;

    public function __construct(
        string $label,
        private mixed $value,
        ?bool $disabled = null,
    )
    {
        $this->label = $label;
        $this->disabled = $disabled;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderRadio($this, ...$extensions);
    }
}
