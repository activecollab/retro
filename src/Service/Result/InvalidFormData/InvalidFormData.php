<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Service\Result\InvalidFormData;

use ActiveCollab\Retro\FormData\FormDataInterface;
use LogicException;
use ActiveCollab\Retro\Service\Result\ServiceResult;

class InvalidFormData extends ServiceResult implements InvalidFormDataInterface
{
    private FormDataInterface $formData;

    public function __construct(FormDataInterface $formData)
    {
        if (!$formData->hasErrors()) {
            throw new LogicException('Form data with errors expected.');
        }

        $this->formData = $formData;
    }

    public function getFormData(): FormDataInterface
    {
        return $this->formData;
    }

    public function isSuccess(): bool
    {
        return false;
    }
}
