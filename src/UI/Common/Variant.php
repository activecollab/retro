<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common;

enum Variant: string
{
    case DEFAULT = 'default';
    case PRIMARY = 'primary';
    case SUCCESS = 'success';
    case NEUTRAL = 'neutral';
    case WARNING = 'warning';
    case DANGER = 'danger';
}
