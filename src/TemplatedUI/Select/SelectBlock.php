<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Select;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\UI\Common\Size;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Form\Select\Select;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class SelectBlock extends WrapContentBlock
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(
        string $content,
        string $name,
        mixed $value = null,
        ?string $label = null,
        ?string $placeholder = null,
        ?string $helpText = null,
        ?Size $size = null,
        ?string $injectAttributes = 'x-inject-form-field-attributes',
        ?FormDataInterface $formData = null,
    ): string
    {
        if ($value === null) {
            $value = $formData?->getFieldValue($name);
        }

        return $this->renderer->renderSelect(
            (new Select(
                $name,
                $label,
                $value,
                new PreRenderedElement($content),
            ))
                ->placeholder($placeholder)
                ->explainer($helpText)
                ->size($size)
                ->injectAttributeName($injectAttributes),
        );
    }
}
