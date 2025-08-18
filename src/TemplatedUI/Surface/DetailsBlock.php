<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Surface;

use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Indicator\Icon;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Surface\Details\Details;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class DetailsBlock extends WrapContentBlock
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(
        string $label,
        string $content,
        bool $open = false,
        ?bool $disabled = null,
        ?string $expandIcon = null,
        ?string $collapseIcon = null,
    ): string
    {
        return $this->renderer->renderDetails(
            (new Details(
                $label,
                new PreRenderedElement($content),
                $open,
                $disabled,
            ))->icon(
                $expandIcon ? new Icon($expandIcon) : null,
                $collapseIcon ? new Icon($collapseIcon) : null,
            ),
        );
    }
}
