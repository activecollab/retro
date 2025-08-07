<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Button;

use ActiveCollab\Retro\TemplatedUI\Property\ButtonStyle;
use ActiveCollab\Retro\TemplatedUI\Property\Size;
use ActiveCollab\Retro\TemplatedUI\Property\ButtonVariant;
use ActiveCollab\Retro\TemplatedUI\Property\Width;
use ActiveCollab\Retro\UI\Action\ActionInterface;
use ActiveCollab\TemplatedUI\MethodInvoker\CatchAllParameters\CatchAllParametersInterface;

interface ButtonBlockInterface
{
    public function render(
        string $content,
        ?ActionInterface $action = null,
        ?ButtonVariant $variant = null,
        ?ButtonStyle $style = null,
        ?Size $size = null,
        ?Width $width = null,
        ?CatchAllParametersInterface $catchAllParameters = null,
    ): string;
}
