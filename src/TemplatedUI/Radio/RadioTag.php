<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Radio;

use ActiveCollab\Retro\TemplatedUI\Property\Size;
use ActiveCollab\TemplatedUI\Tag\Tag;

class RadioTag extends Tag
{
    public function render(
        string $label,
        mixed $value,
        bool $disabled = false,
        ?Size $size = null,
    ): string
    {
        $attributes = [
            'value' => $value,
            'disabled' => $disabled,
        ];

        if ($size) {
            $attributes['size'] = $size->toAttributeValue();
        }

        return sprintf(
            '%s%s%s',
            $this->openHtmlTag('sl-radio', $attributes),
            $this->sanitizeForHtml($label),
            $this->closeHtmlTag('sl-radio'),
        );
    }
}
