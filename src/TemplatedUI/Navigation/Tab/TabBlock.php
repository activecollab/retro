<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Navigation\Tab;

use ActiveCollab\Retro\UI\Navigation\Tab\Tab;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class TabBlock extends WrapContentBlock
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(
        string $content,
        string $label,
        ?string $name = null,
    ): string
    {
        return $this->renderer->renderTab(
            new Tab(
                $label,
                $content,
                $name,
            ),
        );
    }
}
