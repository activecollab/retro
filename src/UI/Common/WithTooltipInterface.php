<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common;

use ActiveCollab\Retro\UI\Indicator\Tooltip\TooltipInterface;

interface WithTooltipInterface
{
    public function getTooltip(): ?TooltipInterface;
}
