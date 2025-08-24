<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property;

use ActiveCollab\Retro\UI\Common\AdornmentInterface;

interface WithAdornmentsInterface extends WithLeftAdornmentInterface, WithRightAdornmentInterface
{
    public function adornments(
        ?AdornmentInterface $leftAdornment,
        ?AdornmentInterface $rightAdornment,
    ): static;
}
