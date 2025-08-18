<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property\Trait;

trait WithDisabledTrait
{
    private ?bool $disabled = null;

    public function disabled(?bool $disabled): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function isDisabled(): ?bool
    {
        return $this->disabled;
    }
}
