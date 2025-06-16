<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Radio;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\TemplatedUI\Property\Size;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class RadioGroupBlock extends WrapContentBlock
{
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
        $attributes = [
            'name' => $name,
            'label' => $label,
        ];

        if ($value === null) {
            $value = $formData?->getFieldValue($name);
        }

        if ($value !== null) {
            $attributes['value'] = $value;
        }

        if ($helpText) {
            $attributes['help-text'] = $helpText;
        }

        if ($size) {
            $attributes['size'] = $size->toAttributeValue();
        }

        return sprintf(
            '%s%s%s',
            $this->openHtmlTag(
                'sl-radio-group',
                $attributes,
            ),
            $content,
            $this->closeHtmlTag('sl-radio-group'),
        );
    }
}
