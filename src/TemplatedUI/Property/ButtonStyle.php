<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Property;

enum ButtonStyle
{
    case OUTLINE;
    case PILL;
    case CIRCLE;
    case LOADING;

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
