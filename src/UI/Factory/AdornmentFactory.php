<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Factory;

use ActiveCollab\Retro\UI\Indicator\Badge;
use ActiveCollab\Retro\UI\Indicator\BadgeInterface;
use ActiveCollab\Retro\UI\Indicator\Icon;
use ActiveCollab\Retro\UI\Indicator\IconInterface;

class AdornmentFactory implements AdornmentFactoryInterface
{
    public function icon(string $iconName): IconInterface
    {
        return new Icon($iconName);
    }

    public function badge(mixed $value): BadgeInterface
    {
        return new Badge((string) $value);
    }
}
