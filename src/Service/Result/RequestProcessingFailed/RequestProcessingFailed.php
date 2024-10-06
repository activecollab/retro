<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Service\Result\RequestProcessingFailed;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\Service\Result\ServiceResult;
use Exception;

class RequestProcessingFailed extends ServiceResult implements RequestProcessingFailedInterface
{
    public function __construct(
        private Exception $reason,
        private ?FormDataInterface $formData,
    )
    {
    }

    public function getReason(): Exception
    {
        return $this->reason;
    }

    public function getFormData(): ?FormDataInterface
    {
        return $this->formData;
    }

    public function isSuccess(): bool
    {
        return false;
    }
}
