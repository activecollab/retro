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
    public function render(
        string $content,
        string $justifyContent = 'center',
        string $alignItems = 'center',
        string $width = 'fit-content',
    ): string
    {
        return sprintf(
            '<div style="display: flex; justify-content: %s; align-items: %s; width: %s; gap: 24px;">%s</div>',
            $justifyContent,
            $alignItems,
            $width,
            $content,
        );
    }
}
