<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property\Trait;

use ActiveCollab\Retro\UI\Common\AdornmentInterface;

trait WithAdornmentsTrait
{
    use WithLeftAdornmentTrait;
    use WithRightAdornmentTrait;

    public function adornments(
        ?AdornmentInterface $leftAdornment,
        ?AdornmentInterface $rightAdornment,
    ): static
    {
        $this->leftAdornment = $leftAdornment;
        $this->rightAdornment = $rightAdornment;

        return $this;
    }
}
