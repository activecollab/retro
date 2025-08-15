<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Indicator;

use ActiveCollab\Retro\UI\Common\Variant;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface InfoBoxInterface extends RenderableElementInterface
{
    public function getContent(): PreRenderedElement|string;
    public function getVariant(): Variant;
    public function getIcon(): ?IconInterface;
}
