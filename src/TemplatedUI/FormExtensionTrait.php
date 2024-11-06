<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\Service\Result\ServiceResultInterface;

trait FormExtensionTrait
{
    protected function withCommonVariables(
        ?ServiceResultInterface $serviceProcessingResult,
        ?FormDataInterface $formData,
        ?string $target,
        array $variables = [],
    ): array
    {
        return array_merge(
            $variables,
            [
                'serviceProcessingResult' => $serviceProcessingResult,
                'formData' => $formData,
                'target' => $target,
            ],
        );
    }
}
