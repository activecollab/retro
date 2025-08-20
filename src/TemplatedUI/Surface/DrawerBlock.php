<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Surface;

use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Surface\Drawer\Drawer;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class DrawerBlock extends WrapContentBlock
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(
        string $label,
        string $content,
        ?string $id = null,
    ): string
    {
        return $this->renderer->renderDrawer(
            (new Drawer(
                $label,
                new PreRenderedElement($content),
            ))->id($id),
        );
    }
}
