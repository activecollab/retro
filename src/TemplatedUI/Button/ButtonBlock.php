<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Button;

use ActiveCollab\Retro\TemplatedUI\Property\ButtonStyle;
use ActiveCollab\Retro\TemplatedUI\Property\Size;
use ActiveCollab\Retro\TemplatedUI\Property\ButtonVariant;
use ActiveCollab\Retro\TemplatedUI\Property\Width;
use ActiveCollab\TemplatedUI\MethodInvoker\CatchAllParameters\CatchAllParametersInterface;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;
use LogicException;

class ButtonBlock extends WrapContentBlock implements ButtonBlockInterface
{
    public function render(
        string $content,
        ?ButtonVariant $variant = null,
        ?ButtonStyle $style = null,
        ?Size $size = null,
        ?Width $width = null,
        ?CatchAllParametersInterface $catchAllParameters = null,
    ): string
    {
        $attributes = [
            'type' => $catchAllParameters
                ? $this->getButtonType($catchAllParameters)
                : 'button',
            'variant' => $variant
                ? $variant->toAttributeValue()
                : ButtonVariant::PRIMARY->toAttributeValue(),
        ];

        if ($size) {
            $attributes['size'] = $size->toAttributeValue();
        }

        if ($style) {
            $attributes[$style->toAttributeName()] = true;
        }

        return sprintf(
            '%s%s%s',
            $this->openHtmlTag('sl-button', $attributes),
            $content,
            $this->closeHtmlTag('button'),
        );
    }

    private function getButtonType(CatchAllParametersInterface $catchAllParameters): string
    {
        if ($this->allowButtonTypeOverride()) {
            return $catchAllParameters->getParameter('type', $this->getDefaultButtonType());
        }

        if ($catchAllParameters->hasParameter('type')) {
            throw new LogicException(
                sprintf(
                    'Button "%s" does not allow type override.',
                    $this::class,
                ),
            );
        }

        return $this->getDefaultButtonType();
    }

    protected function allowButtonTypeOverride(): bool
    {
        return true;
    }

    protected function getDefaultButtonType(): string
    {
        return 'button';
    }
}
