<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\FormData\Factory;

use ActiveCollab\PhoneNumber\Factory\PhoneNumberFactoryInterface;
use ActiveCollab\Retro\FormData\FormData;
use ActiveCollab\Retro\FormData\FormDataInterface;
use Psr\Log\LoggerInterface;

class FormDataFactory implements FormDataFactoryInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private PhoneNumberFactoryInterface $phoneNumberFactory,
    )
    {
    }

    public function createFormData(
        array $fieldValues = [],
        array $fieldErrors = [],
    ): FormDataInterface
    {
        return new FormData(
            $this->logger,
            $this->phoneNumberFactory,
            $fieldValues,
            $fieldErrors,
        );
    }
}
