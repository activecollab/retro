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

        if (!empty($attributes['x-model'])) {
            $attributes['x-model'] = sprintf('formData.%s', $attributes['x-model']);
        }

        if ($placeholder) {
            $attributes['placeholder'] = $placeholder;
        }

        return $this->wrapFormControl(
            $this->openHtmlTag('input', $attributes),
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
