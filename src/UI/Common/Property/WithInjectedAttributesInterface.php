<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property;

interface WithInjectedAttributesInterface
{
    public function injectAttributeName(?string $injectAttributeName): static;
    public function getInjectAttributeName(): ?string;
}
