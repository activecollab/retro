<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Action\Trait;

trait SwapTrait
{
    private ?string $swap = null;

    public function getSwap(): ?string
    {
        return $this->swap;
    }

    public function swap(string $swap): static
    {
        $this->swap = $swap;

        return $this;
    }

    private function applySwap(array $attributes): array
    {
        if ($this->swap) {
            $attributes['hx-swap'] = $this->swap;
        }

        return $attributes;
    }
}
