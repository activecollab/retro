<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Form\Radio;

use ActiveCollab\Retro\UI\Common\Property\WithDisabledInterface;
use ActiveCollab\Retro\UI\Common\Property\WithRequiredLabelInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface RadioInterface extends RenderableElementInterface, WithRequiredLabelInterface, WithDisabledInterface
{
    public function getValue(): mixed;
}
