<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Input;

use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class TextareaBlock extends WrapContentBlock
{
    public function render(
        string $content,
        string $placeholder = null,
        string $helpText = null,
        string $resize = 'auto',
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
            $content,
            $this->closeHtmlTag('sl-textarea'),
        );
    }
}
