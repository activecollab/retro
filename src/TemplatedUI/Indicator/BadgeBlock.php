<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Indicator;

use ActiveCollab\Retro\UI\Indicator\Badge;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class BadgeBlock extends WrapContentBlock
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(
        string $content,
    ): string
    {
        return (new Badge($content))->renderUsingRenderer($this->renderer);
    }
}
