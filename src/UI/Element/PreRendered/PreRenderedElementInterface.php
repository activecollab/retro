<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Element\PreRendered;

use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface PreRenderedElementInterface extends RenderableElementInterface
{
    public function getPreRenderedContent(): string;
}
