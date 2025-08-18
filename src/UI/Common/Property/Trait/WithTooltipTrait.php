<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property\Trait;

use ActiveCollab\Retro\UI\Indicator\Tooltip\TooltipInterface;

trait WithTooltipTrait
{
    private ?TooltipInterface $tooltip = null;

    public function tooltip(?TooltipInterface $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function getTooltip(): ?TooltipInterface
    {
        return $this->tooltip;
    }
}
