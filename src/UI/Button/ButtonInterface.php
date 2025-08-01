<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Button;

use ActiveCollab\Retro\TemplatedUI\Property\ButtonStyle;
use ActiveCollab\Retro\TemplatedUI\Property\ButtonVariant;
use ActiveCollab\Retro\TemplatedUI\Property\Size;
use ActiveCollab\Retro\TemplatedUI\Property\Width;
use ActiveCollab\Retro\UI\Action\ActionInterface;
use ActiveCollab\Retro\UI\Common\AdornmentInterface;
use ActiveCollab\Retro\UI\Common\TriggerInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;
use ActiveCollab\Retro\UI\Indicator\IconInterface;

interface ButtonInterface extends RenderableElementInterface, TriggerInterface
{
    public function getContent(): IconInterface|string;
    public function getAction(): ?ActionInterface;
    public function getLeftAdornment(): ?AdornmentInterface;
    public function getRightAdornment(): ?AdornmentInterface;
    public function getType(): ?string;
    public function getVariant(): ?ButtonVariant;
    public function getStyle(): ?ButtonStyle;
    public function getSize(): ?Size;
    public function getWidth(): ?Width;
}
