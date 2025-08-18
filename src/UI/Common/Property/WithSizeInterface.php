<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property;

use ActiveCollab\Retro\UI\Common\Size;

interface WithSizeInterface
{
    public function size(?Size $size): static;
    public function getSize(): ?Size;
}
