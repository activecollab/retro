<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Service\Result\Success;

use ActiveCollab\Retro\Service\Result\ServiceResult;

class Success extends ServiceResult implements SuccessInterface
{
    public function isSuccess(): bool
    {
        return true;
    }
}