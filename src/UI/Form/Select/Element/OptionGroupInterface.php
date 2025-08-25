<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Form\Select\Element;

use ActiveCollab\Retro\UI\Common\Property\WithRequiredLabelInterface;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface OptionGroupInterface extends RenderableElementInterface, ElementInterface, WithRequiredLabelInterface
{
    public function getPreRenderedElement(): ?PreRenderedElementInterface;

    /**
     * @return OptionInterface[]
     */
    public function getOptions(): array;
}
