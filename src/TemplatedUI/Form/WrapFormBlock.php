<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Form;

use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class WrapFormBlock extends WrapContentBlock
{
    public function render(
        string $content,
        string $id,
        ?string $class = null,
    ): string
    {
        $attributes = [
            'id' => $id,
            'hx-ext' => 'response-targets',
        ];

        if ($class) {
            $attributes['class'] = $class;
        }

        return sprintf(
            '%s%s%s',
            $this->openHtmlTag('div', $attributes),
            $content,
            $this->closeHtmlTag('div'),
        );
    }
}
