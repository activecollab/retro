<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Radio;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\UI\Common\Size;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Form\Radio\RadioGroup;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class RadioGroupBlock extends WrapContentBlock
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(
        string $content,
        string $name,
        string $label,
        mixed $value = null,
        ?string $helpText = null,
        ?Size $size = null,
        ?FormDataInterface $formData = null,
    ): string
    {
        if ($value === null) {
            $value = $formData?->getFieldValue($name);
        }

        return $this->renderer->renderRadioGroup(
            (new RadioGroup(
                $name,
                $label,
                $value,
                new PreRenderedElement($content),
            ))->explainer($helpText)->size($size),
        );
    }
}
