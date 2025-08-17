<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Trait;

trait WithLabelTrait
{
    private string $label;

    public function getLabel(): string
    {
        return $this->label;
    }
}
