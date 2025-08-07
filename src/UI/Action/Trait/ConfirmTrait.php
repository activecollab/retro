<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Action\Trait;

trait ConfirmTrait
{
    private ?string $confirm = null;

    public function getConfirm(): ?string
    {
        return $this->confirm;
    }

    public function confirm(string $confirm): static
    {
        $this->confirm = $confirm;

        return $this;
    }

    private function applyConfirm(array $attributes): array
    {
        if ($this->confirm) {
            $attributes['hx-confirm'] = $this->confirm;
        }

        return $attributes;
    }
}
