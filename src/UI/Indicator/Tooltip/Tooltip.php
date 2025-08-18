<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Indicator\Tooltip;

use ActiveCollab\Retro\UI\Common\Property\WithTooltipInterface;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;
use LogicException;

class Tooltip implements TooltipInterface
{
    private ?RenderableElementInterface $wrapAround;

    public function __construct(
        private PreRenderedElementInterface|string $content,
        RenderableElementInterface|null $wrapAround = null,
    )
    {
        if ($wrapAround instanceof WithTooltipInterface && $wrapAround->getTooltip()) {
            throw new LogicException('Element already has a tooltip.');
        }

        $this->wrapAround = $wrapAround;
    }

    public function getContent(): PreRenderedElementInterface|string
    {
        return $this->content;
    }

    public function getWrapAround(): ?RenderableElementInterface
    {
        return $this->wrapAround;
    }
}
