<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Service\Result\Factory;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\Service\Result\InvalidFormData\InvalidFormDataInterface;
use ActiveCollab\Retro\Service\Result\RequestProcessingFailed\RequestProcessingFailedInterface;
use ActiveCollab\Retro\Service\Result\Success\SuccessInterface;
use Exception;

interface ServiceResultFactoryInterface
{
    public function success(): SuccessInterface;
    public function requestProcessingFailed(
        Exception $reason,
        ?FormDataInterface $formData,
    ): RequestProcessingFailedInterface;
    public function invalidFormData(FormDataInterface $formData): InvalidFormDataInterface;
}
