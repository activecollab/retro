<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Input;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\TemplatedUI\Form\FormField\FormFieldTag;
use ActiveCollab\Retro\TemplatedUI\Property\Size;
use ActiveCollab\TemplatedUI\MethodInvoker\CatchAllParameters\CatchAllParametersInterface;

class TextareaTag extends FormFieldTag
{
    public function render(
        string $name,
        string $value = null,
        string $placeholder = null,
        string $helpText = null,
        string $resize = 'auto',
        ?Size $size = null,
        ?CatchAllParametersInterface $catchAllParameters = null,
        ?FormDataInterface $formData = null,
    ): string
    {
        $attributes = $this->withInjectPlaceholder(
            $catchAllParameters ? $catchAllParameters->getParameters() : [],
            [
                'name' => $name,
                'class' => implode(' ', $this->catchAllClasses($catchAllParameters)),
            ],
        );

        if ($placeholder) {
            $attributes['placeholder'] = $placeholder;
        }

        if ($helpText) {
            $attributes['help-text'] = $helpText;
        }

        if ($resize === 'auto' || $resize === 'none') {
            $attributes['resize'] = $resize;
        }

        return sprintf(
            '%s%s%s',
            $this->openHtmlTag('sl-textarea', $attributes),
            $value ?? $formData?->getFieldValue($name),
            $this->closeHtmlTag('sl-textarea'),
        );
    }
}
