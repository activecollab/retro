<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Select;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\TemplatedUI\Form\FormField\FormFieldInterface;
use ActiveCollab\Retro\TemplatedUI\Form\FormField\FormFieldTag;

abstract class SelectTag extends FormFieldTag
{
    protected function openSelectTag(
        string $name,
        string $class = '',
    ): string
    {
        $classes = [];

        return $this->openHtmlTag(
            'sl-select',
            $this->withInjectPlaceholder(
                [
                    'name' => $name,
                    'class' => $this->prepareClassAttribute($class, ...$classes),
                ],
            ),
        );
    }

    protected function closeSelectTag(): string
    {
        return '</sl-select>';
    }

    protected function openOptionalSelectTag(
        string $name,
        ?FormDataInterface $formData = null,
        string $class = '',
    ): string
    {
        return sprintf(
            '%s%s%s',
            $this->openSelectTag($name, $class),
            $this->renderOption('None', '', empty($formData?->getFieldValue($name))),
            $this->renderOption(''),
        );
    }

    protected function renderOptionGroup(string $label): string
    {
        return $this->openHtmlTag(
            'optgroup',
            [
                'label' => $this->sanitizeForHtml($label),
            ],
        );
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
            $this->closeHtmlTag('option'),
        );
    }
}
