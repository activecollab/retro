<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI;

use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\TemplatedUI\Tag\Tag;

class DividerTag extends Tag
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(): string
    {
        return '<sl-divider></sl-divider>';
    }
}
