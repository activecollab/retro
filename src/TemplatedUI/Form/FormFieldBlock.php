<?php

/*
 * This file is part of the Feud project.
 *
 * (c) PhpCloud.org Core Team <core@phpcloud.org>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Form;

use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\TemplatedUI\ComponentIdResolver\ComponentIdResolverInterface;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class FormFieldBlock extends WrapContentBlock
{
    public function __construct(
        private ComponentIdResolverInterface $componentIdResolver,
    )
    {
    }

    public function render(
        string $fieldName,
        string $content,
        string $id = null,
        bool $required = false,
        FormDataInterface $formData = null,
    ): string
    {
        $id = $id ?? $this->componentIdResolver->getUniqueId($fieldName);

        return sprintf(
            '<div class="%s" data-form-field="%s">%s%s</div>',
            implode(' ', $this->getWrapperClasses($fieldName, $formData)),
            $fieldName,
            str_replace(
                'x-inject-form-field-attributes=""',
                $this->injectFormFieldAttributes($id, $required),
                $content,
            ),
            $this->renderFieldErrors($fieldName, $id, $formData),
        );
    }

    private function getWrapperClasses(
        string $fieldName,
        ?FormDataInterface $formData
    ): array
    {
        $result = [
            'form-field',
        ];

        if ($formData?->hasFieldErrors($fieldName)) {
            $result[] = 'with-errors';
        }

        return $result;
    }

    private function injectFormFieldAttributes(
        string $fieldId,
        bool $required,
    ): string
    {
        $attributes = [
            sprintf('id="%s"', $fieldId),
        ];

        if ($required) {
            $attributes[] = 'required';
        }

        return implode(' ', $attributes);
    }

    private function renderFieldErrors(
        string $fieldName,
        string $fieldId,
        ?FormDataInterface $formData
    ): string
    {
        if ($formData === null) {
            return '';
        }

        if (!$formData->hasFieldErrors($fieldName)) {
            return '';
        }

        return implode('<br>', $formData->getFieldErrors($fieldName));
    }
}
