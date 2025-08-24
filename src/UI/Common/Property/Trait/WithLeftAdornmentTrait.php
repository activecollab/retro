<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property\Trait;

use ActiveCollab\Retro\UI\Common\AdornmentInterface;

trait WithLeftAdornmentTrait
{
    private ?AdornmentInterface $leftAdornment = null;

    public function leftAdornment(?AdornmentInterface $leftAdornment): static
    {
        $this->leftAdornment = $leftAdornment;

        return $this;
    }

    public function getLeftAdornment(): ?AdornmentInterface
    {
        return $this->leftAdornment;
    }
}
