<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Checkbox;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\TemplatedUI\Property\Size;
use ActiveCollab\TemplatedUI\Tag\Tag;

class CheckboxTag extends Tag
{
    public function render(
        string $name,
        string $label,
        bool $checked = false,
        mixed $value = '1',
        ?string $helpText = null,
        ?Size $size = null,
        ?FormDataInterface $formData = null,
    ): string
    {
        $attributes = [
            'name' => $name,
            'value' => $value,
            'checked' => $checked || !empty($formData?->getFieldValue($name)),
        ];

        if ($checked) {
            $attributes['checked'] = true;
        }

        if ($helpText) {
            $attributes['helpText'] = $helpText;
        }

        if ($size) {
            $attributes['size'] = $size->toAttributeValue();
        }

        return sprintf(
            '%s%s%s',
            $this->openHtmlTag(
                'sl-checkbox',
                $attributes,
            ),
            $this->sanitizeForHtml($label),
            $this->closeHtmlTag('sl-checkbox'),
        );
    }
}
