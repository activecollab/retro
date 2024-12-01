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
        ?string $label = null,
        ?string $id = null,
        ?string $class = null,
        bool $required = false,
        ?FormDataInterface $formData = null,
    ): string
    {
        $id = $id ?? $this->componentIdResolver->getUniqueId($fieldName);

        return sprintf(
            '<div class="%s" data-form-field="%s">%s%s</div>',
            implode(' ', $this->getWrapperClasses($fieldName, $class, $formData)),
            $fieldName,
            str_replace(
                'x-inject-form-field-attributes=""',
                $this->injectFormFieldAttributes($id, $label, $required),
                $content,
            ),
            $this->renderFieldErrors($fieldName, $id, $formData),
        );
    }

    private function getWrapperClasses(
        string $fieldName,
        ?string $class,
        ?FormDataInterface $formData,
    ): array
    {
        $result = array_merge(
            explode(' ', $class ?? ''),
            [
                'form-field',
            ],
        );

        if ($formData?->hasFieldErrors($fieldName)) {
            $result[] = 'with-errors';
        }

        return $result;
    }

    private function injectFormFieldAttributes(
        string $fieldId,
        ?string $label,
        bool $required,
    ): string
    {
        $attributes = [
            sprintf('id="%s"', $fieldId),
        ];

        if ($label) {
            $attributes[] = sprintf('label="%s"', $this->sanitizeForHtml($label));
        }

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
