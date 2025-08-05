<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Property;

enum ButtonStyle: string
{
    case OUTLINE = 'outline';
    case PILL = 'pill';
    case CIRCLE = 'circle';
    case LOADING = 'loading';

    public function toAttributeName(): string
    {
        return match($this) {
            self::OUTLINE => 'outline',
            self::PILL => 'pill',
            self::CIRCLE => 'circle',
            self::LOADING => 'loading',
        };
    }
}
