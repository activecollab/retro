<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Decorator\Color;

interface ColorInterface
{
    const COLOR_SLATE = 'slate';
    const COLOR_GRAY = 'gray';
    const COLOR_ZINC = 'zinc';
    const COLOR_NEUTRAL = 'neutral';
    const COLOR_STONE = 'stone';
    const COLOR_RED = 'red';
    const COLOR_ORANGE = 'orange';
    const COLOR_AMBER = 'amber';
    const COLOR_YELLOW = 'yellow';
    const COLOR_LIME = 'lime';
    const COLOR_GREEN = 'green';
    const COLOR_EMERALD = 'emerald';
    const COLOR_TEAL = 'teal';
    const COLOR_CYAN = 'cyan';
    const COLOR_SKY = 'sky';
    const COLOR_BLUE = 'blue';
    const COLOR_INDIGO = 'indigo';
    const COLOR_VIOLET = 'violet';
    const COLOR_PURPLE = 'purple';
    const COLOR_FUCHSIA = 'fuchsia';
    const COLOR_PINK = 'pink';
    const COLOR_ROSE = 'rose';

    const COLORS = [
        self::COLOR_SLATE,
        self::COLOR_GRAY,
        self::COLOR_ZINC,
        self::COLOR_NEUTRAL,
        self::COLOR_STONE,
        self::COLOR_RED,
        self::COLOR_ORANGE,
        self::COLOR_AMBER,
        self::COLOR_YELLOW,
        self::COLOR_LIME,
        self::COLOR_GREEN,
        self::COLOR_EMERALD,
        self::COLOR_TEAL,
        self::COLOR_CYAN,
        self::COLOR_SKY,
        self::COLOR_BLUE,
        self::COLOR_INDIGO,
        self::COLOR_VIOLET,
        self::COLOR_PURPLE,
        self::COLOR_FUCHSIA,
        self::COLOR_PINK,
        self::COLOR_ROSE,
    ];

    const SHADE_50 = 50;
    const SHADE_100 = 100;
    const SHADE_200 = 200;
    const SHADE_300 = 300;
    const SHADE_400 = 400;
    const SHADE_500 = 500;
    const SHADE_600 = 600;
    const SHADE_700 = 700;
    const SHADE_800 = 800;
    const SHADE_900 = 900;

    const SHADES = [
        self::SHADE_50,
        self::SHADE_100,
        self::SHADE_200,
        self::SHADE_300,
        self::SHADE_400,
        self::SHADE_500,
        self::SHADE_600,
        self::SHADE_700,
        self::SHADE_800,
        self::SHADE_900,
    ];

    public function getName(): string;
    public function getShade(): int;
}
