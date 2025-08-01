<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Navigation\Tab;

use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class TabsBlock extends WrapContentBlock
{
    public function render(
        string $content,
    ): string
    {
        return sprintf('<sl-tab-group>%s</sl-tab-group>', $content);
    }
}
