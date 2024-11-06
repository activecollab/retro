<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Decorator\Color;

use InvalidArgumentException;

class Color implements ColorInterface
{
    private string $name;
    private int $shade;

    public function __construct(
        string $name,
        int $shade = 500,
    )
    {
        if (!in_array($name, self::COLORS)) {
            throw new InvalidArgumentException(
                sprintf('Color "%s" is not supported.', $name)
            );
        }

        if (!in_array($shade, self::SHADES)) {
            throw new InvalidArgumentException(
                sprintf('Shade "%d" is not supported.', $shade)
            );
        }

        $this->name = $name;
        $this->shade = $shade;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShade(): int
    {
        return $this->shade;
    }

    public function __toString(): string
    {
        return sprintf('%s-%d', $this->getName(), $this->getShade());
    }
}
