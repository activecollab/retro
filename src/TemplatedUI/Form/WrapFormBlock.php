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
    public function render(string $content, string $id): string
    {
        return sprintf(
            '<div id="%s" hx-ext="response-targets">%s</div>',
            $this->sanitizeForHtml($id),
            $content,
        );
    }
}
