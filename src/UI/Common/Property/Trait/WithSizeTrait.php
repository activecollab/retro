<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property\Trait;

use ActiveCollab\Retro\UI\Common\Size;

trait WithSizeTrait
{
    private ?Size $size = null;

    public function size(?Size $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): ?Size
    {
        return $this->size;
    }
}
