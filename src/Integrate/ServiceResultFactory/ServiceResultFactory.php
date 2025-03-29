<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Integrate\ServiceResultFactory;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\Service\Result\Factory\ServiceResultFactoryInterface;
use ActiveCollab\Retro\Service\Result\InvalidFormData\InvalidFormData;
use ActiveCollab\Retro\Service\Result\InvalidFormData\InvalidFormDataInterface;
use ActiveCollab\Retro\Service\Result\RequestProcessingFailed\RequestProcessingFailed;
use ActiveCollab\Retro\Service\Result\RequestProcessingFailed\RequestProcessingFailedInterface;
use ActiveCollab\Retro\Service\Result\Success\Success;
use ActiveCollab\Retro\Service\Result\Success\SuccessInterface;
use Exception;

abstract class ServiceResultFactory implements ServiceResultFactoryInterface
{
    public function success(): SuccessInterface
    {
        return new Success();
    }

    public function requestProcessingFailed(
        Exception $reason,
        ?FormDataInterface $formData,
    ): RequestProcessingFailedInterface
    {
        return new RequestProcessingFailed($reason, $formData);
    }

    public function invalidFormData(FormDataInterface $formData): InvalidFormDataInterface
    {
        return new InvalidFormData($formData);
    }
}
