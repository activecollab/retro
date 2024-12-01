<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Form;

use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class WrapButtonsBlock extends WrapContentBlock
{
    public function render(
        string $content,
        ?string $class = null,
    ): string
    {
        return sprintf(
            '%s%s%s',
            $this->openHtmlTag(
                'div',
                [
                    'class' => $class ?? '',
                ],
            ),
            $content,
            $this->closeHtmlTag('div'),
        );
    }
}
