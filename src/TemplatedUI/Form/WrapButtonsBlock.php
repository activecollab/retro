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
    public function render(string $content): string
    {
        return sprintf('<div style="margin-top: 0.25rem;">%s</div>', $content);
    }
}
