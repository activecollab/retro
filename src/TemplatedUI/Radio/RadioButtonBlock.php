<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Radio;

use ActiveCollab\Retro\UI\Common\Size;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class RadioButtonBlock extends WrapContentBlock
{
    public function render(
        string $content,
        mixed $value,
        bool $disabled = false,
        bool $pill = false,
        ?Size $size = null,
    ): string
    {
        $attributes = [
            'value' => $value,
            'disabled' => $disabled,
            'pill' => $pill,
        ];

        if ($size) {
            $attributes['size'] = $size->value;
        }

        return sprintf(
            '%s%s%s',
            $this->openHtmlTag(
                'sl-radio-button',
                $attributes,
            ),
            $content,
            $this->closeHtmlTag('sl-radio-button'),
        );
    }
}
