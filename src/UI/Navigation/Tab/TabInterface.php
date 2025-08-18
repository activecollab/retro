<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Navigation\Tab;

use ActiveCollab\Retro\UI\Common\Property\WithNameInterface;
use ActiveCollab\Retro\UI\Common\Property\WithRequiredLabelInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface TabInterface extends RenderableElementInterface, WithNameInterface, WithRequiredLabelInterface
{
    public function getContent(): string;
}
