<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Property;

interface WithRequiredLabelInterface
{
    public function label(string $label): static;
    public function getLabel(): string;
}
