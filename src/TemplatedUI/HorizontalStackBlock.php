<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI;

use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class HorizontalStackBlock extends WrapContentBlock
{
    public function render(string $content): string
    {
        return sprintf('<div style="display: flex; justify-content: center; align-items: center;width: fit-content; gap: 24px;">%s</div>', $content);
    }
}
