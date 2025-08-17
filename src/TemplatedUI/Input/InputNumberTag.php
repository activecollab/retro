<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Input;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\TemplatedUI\Input\Type\InputType;
use ActiveCollab\Retro\UI\Common\Size;
use ActiveCollab\TemplatedUI\MethodInvoker\CatchAllParameters\CatchAllParametersInterface;

class InputNumberTag extends InputTag
{
    private int|float $step = 1;

    public function render(
        string $name,
        int|float $step = 1,
        string $placeholder = null,
        string $helpText = null,
        ?Size $size = null,
        ?FormDataInterface $formData = null,
        ?CatchAllParametersInterface $catchAllParameters = null,
    ): string
    {
        $this->step = $step;

        return $this->renderInput(
            new InputType('number'),
            $name,
            null,
            $placeholder,
            $helpText,
            $size,
            $catchAllParameters,
            $formData,
        );
    }

    protected function getInputAttributes(): array
    {
        return array_merge(
            parent::getInputAttributes(),
            [
                'step' => $this->step,
            ],
        );
    }
}
