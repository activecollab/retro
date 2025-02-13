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

abstract class InputTag extends FormFieldTag
{
    protected function renderInput(
        string $type,
        string $name,
        ?string $value,
        ?string $placeholder,
        ?Size $size = null,
        ?CatchAllParametersInterface $catchAllParameters = null,
        ?FormDataInterface $formData = null,
    ): string
    {
        $attributes = $this->withInjectPlaceholder(
            $this->getInputAttributes(),
            $catchAllParameters ? $catchAllParameters->getParameters() : [],
            [
                'type' => $type,
                'name' => $name,
                'class' => implode(' ', $this->getInputClasses($catchAllParameters)),
                'value' => $value ?? $formData?->getFieldValue($name),
            ],
        );

        if ($placeholder) {
            $attributes['placeholder'] = $placeholder;
        }

        return $this->wrapFormControl(
            sprintf(
                '%s%s',
                $this->openHtmlTag('sl-input', $attributes),
                $this->closeHtmlTag('sl-input'),
            )
        );
    }

    private function getInputClasses(?CatchAllParametersInterface $catchAllParameters): array
    {
        return $catchAllParameters && $catchAllParameters->getParameter('class')
            ? explode(' ', $catchAllParameters->getParameter('class'))
            : [];
    }

    protected function getInputAttributes(): array
    {
        return [];
    }
}
