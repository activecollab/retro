<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property\Trait;

use ActiveCollab\Retro\UI\Common\Variant;

trait WithVariantTrait
{
    private ?Variant $variant = null;

    public function variant(?Variant $variant): static
    {
        $this->variant = $variant;

        return $this;
    }

    public function getVariant(): ?Variant
    {
        return $this->variant;
    }
}
