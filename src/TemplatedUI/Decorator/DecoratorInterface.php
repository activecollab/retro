<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Decorator;

use ActiveCollab\Retro\TemplatedUI\Decorator\Color\ColorInterface;

interface DecoratorInterface
{
    public function getAlertColor(int $shade = ColorInterface::SHADE_500): ColorInterface;
}
