<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Indicator\Tooltip;

use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface TooltipInterface
{
    public function getContent(): PreRenderedElementInterface|string;
    public function getWrapAround(): ?RenderableElementInterface;
}
