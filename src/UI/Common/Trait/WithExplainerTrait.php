<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Common\Trait;

trait WithExplainerTrait
{
    private ?string $explainer = null;

    public function getExplainer(): ?string
    {
        return $this->explainer;
    }
}
