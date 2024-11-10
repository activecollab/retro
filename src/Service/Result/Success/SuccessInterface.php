<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Service\Result\Success;

use ActiveCollab\Retro\Service\Result\ServiceResultInterface;

interface SuccessInterface extends ServiceResultInterface
{
    public function isSuccess(): bool;
}