<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Input;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\TemplatedUI\Property\Size;
use ActiveCollab\TemplatedUI\MethodInvoker\CatchAllParameters\CatchAllParametersInterface;

class InputEmailTag extends InputTag
{
    public function render(
        string $name,
        string $placeholder = null,
        ?Size $size = null,
        ?FormDataInterface $formData = null,
        ?CatchAllParametersInterface $catchAllParameters = null,
    ): string
    {
        return $this->renderInput(
            'email',
            $name,
            null,
            $placeholder,
            $size,
            $catchAllParameters,
            $formData,
        );
    }
}
