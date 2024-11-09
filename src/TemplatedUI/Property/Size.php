<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Property;

enum Size
{
    case SMALL;
    case MEDIUM;
    case LARGE;

    public function toAttributeValue(): string
    {
        return match($this) {
            self::SMALL => 'small',
            self::MEDIUM => 'medium',
            self::LARGE => 'large',
        };
    }
}
