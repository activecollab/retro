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

class TextareaTag extends FormFieldTag
{
    public function render(
        string $name,
        string $value = null,
        string $placeholder = null,
        string $helpText = null,
        string $resize = 'auto',
        ?FormDataInterface $formData = null,
    ): string
    {
        $attributes = [];

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
