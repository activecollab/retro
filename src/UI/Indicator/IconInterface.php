<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Indicator;

use ActiveCollab\Retro\UI\Common\AdornmentInterface;
use ActiveCollab\Retro\UI\Common\Property\WithTooltipInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface IconInterface extends RenderableElementInterface, AdornmentInterface, WithTooltipInterface
{
    public function getIconName(): string;
    public function getLabel(): ?string;
}
