<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property;

use ActiveCollab\Retro\UI\Indicator\Tooltip\TooltipInterface;

interface WithTooltipInterface
{
    public function tooltip(TooltipInterface|string|null $tooltip): static;
    public function getTooltip(): ?TooltipInterface;
}
