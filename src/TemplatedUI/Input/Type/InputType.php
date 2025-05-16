<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Input\Type;

class InputType implements InputTypeInterface
{
    public function __construct(
        private string $type,
    )
    {
    }

    public function getType(): string
    {
        return $this->type;
    }
}