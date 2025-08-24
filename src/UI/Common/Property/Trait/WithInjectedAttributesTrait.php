<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property\Trait;

trait WithInjectedAttributesTrait
{
    private ?string $inject_attribute_name = null;

    public function injectAttributeName(?string $injectAttributeName): static
    {
        $this->inject_attribute_name = $injectAttributeName;

        return $this;
    }

    public function getInjectAttributeName(): ?string
    {
        return $this->inject_attribute_name;
    }
}
