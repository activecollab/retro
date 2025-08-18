<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property;

interface WithRequiredNameInterface
{
    public function name(string $name): static;
    public function getName(): string;
}
