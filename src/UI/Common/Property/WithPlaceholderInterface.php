<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property;

interface WithPlaceholderInterface
{
    public function placeholder(?string $placeholder): static;
    public function getPlaceholder(): ?string;
}
