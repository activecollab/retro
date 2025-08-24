<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Factory;

use ActiveCollab\Retro\UI\Indicator\BadgeInterface;
use ActiveCollab\Retro\UI\Indicator\IconInterface;

interface AdornmentFactoryInterface
{
    public function icon(string $iconName): IconInterface;
    public function badge(mixed $value): BadgeInterface;
}
