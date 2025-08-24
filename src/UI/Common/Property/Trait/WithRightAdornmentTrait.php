<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property\Trait;

use ActiveCollab\Retro\UI\Common\AdornmentInterface;

trait WithRightAdornmentTrait
{
    private ?AdornmentInterface $rightAdornment = null;

    public function rightAdornment(?AdornmentInterface $rightAdornment): static
    {
        $this->rightAdornment = $rightAdornment;

        return $this;
    }

    public function getRightAdornment(): ?AdornmentInterface
    {
        return $this->rightAdornment;
    }
}
