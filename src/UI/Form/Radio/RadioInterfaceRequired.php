<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Form\Radio;

use ActiveCollab\Retro\UI\Common\WithRequiredLabelInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface RadioInterfaceRequired extends RenderableElementInterface, WithRequiredLabelInterface
{
    public function getValue(): mixed;
    public function isDisabled(): bool;
}
