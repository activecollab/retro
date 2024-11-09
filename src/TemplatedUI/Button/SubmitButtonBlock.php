<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Button;

class SubmitButtonBlock extends ButtonBlock
{
    protected function allowButtonTypeOverride(): bool
    {
        return false;
    }

    protected function getDefaultButtonType(): string
    {
        return 'submit';
    }
}
