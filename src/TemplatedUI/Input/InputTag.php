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
use ActiveCollab\Retro\TemplatedUI\Input\Type\InputTypeInterface;
use ActiveCollab\Retro\TemplatedUI\Property\Size;
use ActiveCollab\TemplatedUI\MethodInvoker\CatchAllParameters\CatchAllParametersInterface;

abstract class InputTag extends FormFieldTag
{
    protected function renderInput(
        InputTypeInterface $inputType,
        string $name,
        ?string $value,
        ?string $placeholder,
        ?string $helpText = null,
        ?Size $size = null,
        ?CatchAllParametersInterface $catchAllParameters = null,
        ?FormDataInterface $formData = null,
    ): string
    {
        $attributes = $this->withInjectPlaceholder(
            $this->getInputAttributes(),
            $catchAllParameters ? $catchAllParameters->getParameters() : [],
            [
                'type' => $inputType->getType(),
                'name' => $name,
                'class' => implode(' ', $this->catchAllClasses($catchAllParameters)),
                'value' => $value ?? $formData?->getFieldValue($name),
            ],
        );

        if ($placeholder) {
            $attributes['placeholder'] = $placeholder;
        }

        if ($helpText) {
            $attributes['help-text'] = $helpText;
        }

        return $this->wrapFormControl(
            sprintf(
                '%s%s%s',
                $this->openHtmlTag('sl-input', $attributes),
                $this->getInputContent(),
                $this->closeHtmlTag('sl-input'),
            )
        );
    }

    protected function getInputAttributes(): array
    {
        return [];
    }

    protected function getInputContent(): string
    {
        return '';
    }
}
