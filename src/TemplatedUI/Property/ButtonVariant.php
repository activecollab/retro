<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Property;

enum ButtonVariant
{
    case DEFAULT;
    case PRIMARY;
    case SUCCESS;
    case NEUTRAL;
    case WARNING;
    case DANGER;

    public function toAttributeValue(): string
    {
        return match($this) {
            self::DEFAULT => 'default',
            self::PRIMARY => 'primary',
            self::SUCCESS => 'success',
            self::NEUTRAL => 'neutral',
            self::WARNING => 'warning',
            self::DANGER => 'danger',
        };
    }
}
