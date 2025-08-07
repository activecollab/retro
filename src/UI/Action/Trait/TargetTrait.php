<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Action\Trait;

trait TargetTrait
{
    private ?string $target = null;

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function target(string $target): static
    {
        $this->target = $target;

        return $this;
    }

    private function applyTarget(array $attributes): array
    {
        if ($this->target) {
            $attributes['hx-target'] = $this->target;
        }

        return $attributes;
    }
}
