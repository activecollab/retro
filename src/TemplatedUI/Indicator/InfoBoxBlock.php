<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Indicator;

use ActiveCollab\Retro\UI\Common\Variant;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Indicator\Icon;
use ActiveCollab\Retro\UI\Indicator\InfoBox;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class InfoBoxBlock extends WrapContentBlock
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(
        string $content,
        Variant|string $variant = Variant::PRIMARY,
        ?string $iconName = null,
    ): string
    {
        return (new InfoBox(
            new PreRenderedElement($content),
            $variant,
            $iconName ? new Icon($iconName) : null,
        ))->renderUsingRenderer($this->renderer);
    }
}
