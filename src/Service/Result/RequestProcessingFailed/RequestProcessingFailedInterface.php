<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Service\Result\RequestProcessingFailed;

use ActiveCollab\Retro\FormData\FormDataInterface;
use Exception;
use ActiveCollab\Retro\Service\Result\ServiceResultInterface;

interface RequestProcessingFailedInterface extends ServiceResultInterface
{
    public function getReason(): Exception;
    public function getFormData(): ?FormDataInterface;
}
