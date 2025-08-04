<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Indicator;

use ActiveCollab\Retro\UI\Indicator\Icon;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\TemplatedUI\Tag\Tag;

class IconTag extends Tag
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(
        string $iconName,
    ): string
    {
        return (new Icon($iconName))->renderUsingRenderer($this->renderer);
    }
}