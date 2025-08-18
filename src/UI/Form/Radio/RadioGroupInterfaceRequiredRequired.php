<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Form\Radio;

use ActiveCollab\Retro\UI\Common\Property\WithExplainerInterface;
use ActiveCollab\Retro\UI\Common\Property\WithRequiredLabelInterface;
use ActiveCollab\Retro\UI\Common\Property\WithRequiredNameInterface;
use ActiveCollab\Retro\UI\Common\Size;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface RadioGroupInterfaceRequiredRequired extends RenderableElementInterface, WithRequiredNameInterface, WithRequiredLabelInterface, WithExplainerInterface
{
    public function getValue(): mixed;
    public function getSize(): ?Size;

    /**
     * @return RadioInterfaceRequired[]
     */
    public function getOptions(): array;
}
