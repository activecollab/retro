<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Service\Result\InvalidFormData;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\Service\Result\ServiceResultInterface;

interface InvalidFormDataInterface extends ServiceResultInterface
{
    public function getFormData(): FormDataInterface;
}
