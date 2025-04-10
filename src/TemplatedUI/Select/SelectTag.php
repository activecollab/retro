<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Select;

use ActiveCollab\Retro\TemplatedUI\Form\FormField\FormFieldTag;

abstract class SelectTag extends FormFieldTag
{
    protected function openSelectTag(
        string $name,
        string $class = '',
        mixed $value = null,
        ?string $placeholder = null,
    ): string
    {
        $attributes = [
            'name' => $name,
            'class' => $this->prepareClassAttribute($class),
        ];

        if ($value !== null) {
            $attributes['value'] = $value;
        }

        if ($placeholder) {
            $attributes['placeholder'] = $this->sanitizeForHtml($placeholder);
        }

        return $this->openHtmlTag(
            'sl-select',
            $this->withInjectPlaceholder($attributes),
        );
    }

    protected function closeSelectTag(string $body = ''): string
    {
        return sprintf('%s</sl-select>', $body);
    }

    protected function closeOptionGroup(): string
    {
        return $this->closeHtmlTag('optgroup');
    }

    protected function renderOption(
        string $text,
        string|int $value = '',
        bool $isSelected = false,
    ): string
    {
        return sprintf(
            '%s%s%s',
            $this->openHtmlTag(
                'sl-option',
                [
                    'value' => (string) $value,
                    'selected' => $isSelected,
                ],
            ),
            $this->sanitizeForHtml($text),
            $this->closeHtmlTag('sl-option'),
        );
    }
}
