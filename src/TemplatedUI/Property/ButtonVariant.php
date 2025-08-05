<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Property;

enum ButtonVariant: string
{
    case DEFAULT = 'default';
    case PRIMARY = 'primary';
    case SUCCESS = 'success';
    case NEUTRAL = 'neutral';
    case WARNING = 'warning';
    case DANGER = 'danger';

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
