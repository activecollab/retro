<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Form\Radio;

use ActiveCollab\Retro\UI\Common\Size;
use ActiveCollab\Retro\UI\Common\WithExplainerInterface;
use ActiveCollab\Retro\UI\Common\WithLabelInterface;
use ActiveCollab\Retro\UI\Common\WithNameInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface RadioGroupInterface extends RenderableElementInterface, WithNameInterface, WithLabelInterface, WithExplainerInterface
{
    public function getValue(): mixed;
    public function getSize(): ?Size;

    /**
     * @return RadioInterface[]
     */
    public function getOptions(): array;
}
