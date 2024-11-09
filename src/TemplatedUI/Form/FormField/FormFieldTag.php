<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Form\FormField;

use ActiveCollab\Retro\TemplatedUI\ComponentIdResolver\ComponentIdResolverInterface;
use ActiveCollab\TemplatedUI\Tag\Tag;

abstract class FormFieldTag extends Tag implements FormFieldInterface
{
    public function __construct(
        protected ComponentIdResolverInterface $componentIdResolver,
    )
    {
    }

    protected function withInjectPlaceholder(array ...$attributeSets): array
    {
        $result = [
            'x-inject-form-field-attributes' => '',
        ];

        foreach ($attributeSets as $attributeSet) {
            $result = array_merge($result, $attributeSet);
        }

        return $result;
    }

    protected function wrapFormControl(string $controlHtml): string
    {
        $wrapperClasses = [
            'class' => 'form-control',
        ];

        return sprintf(
            '%s%s%s',
            $this->openHtmlTag(
                'div',
                [
                    'class' => implode(' ', $wrapperClasses),
                ],
            ),
            $controlHtml,
            $this->closeHtmlTag('div'),
        );
    }
}
