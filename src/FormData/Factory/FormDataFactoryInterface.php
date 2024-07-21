<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\FormData\Factory;

use ActiveCollab\Retro\FormData\FormDataInterface;

interface FormDataFactoryInterface
{
    public function createFormData(
        array $fieldValues = [],
        array $fieldErrors = [],
    ): FormDataInterface;
}
