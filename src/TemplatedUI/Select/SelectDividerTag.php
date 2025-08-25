<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Select;

use ActiveCollab\TemplatedUI\Tag\Tag;

class SelectDividerTag extends Tag
{
    public function render(): string
    {
        return '<sl-divider></sl-divider>';
    }
}
