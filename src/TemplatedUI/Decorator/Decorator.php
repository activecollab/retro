<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Decorator;

use ActiveCollab\Retro\TemplatedUI\Decorator\Color\ColorInterface;
use ActiveCollab\Retro\TemplatedUI\Decorator\Color\Factory\ColorFactoryInterface;

class Decorator implements DecoratorInterface
{
    public function __construct(
        private ColorFactoryInterface $colorFactory,
    )
    {
    }

    public function getAlertColor(int $shade = ColorInterface::SHADE_500): ColorInterface
    {
        return $this->colorFactory->createColor(ColorInterface::COLOR_RED, $shade);
    }
}
