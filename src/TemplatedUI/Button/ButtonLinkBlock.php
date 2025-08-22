<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Button;

use ActiveCollab\Retro\TemplatedUI\Property\ButtonStyle;
use ActiveCollab\Retro\UI\Common\Size;
use ActiveCollab\Retro\TemplatedUI\Property\Width;
use ActiveCollab\Retro\UI\Common\Variant;
use ActiveCollab\TemplatedUI\MethodInvoker\CatchAllParameters\CatchAllParametersInterface;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;
use LogicException;

class ButtonLinkBlock extends WrapContentBlock
{
    public function render(
        string $content,
        string $href,
        string $target = 'body',
        ?Variant $variant = null,
        ?ButtonStyle $style = null,
        ?Size $size = null,
        ?Width $width = null,
        ?CatchAllParametersInterface $catchAllParameters = null,
    ): string
    {
        $attributes = [
            'hx-get' => $href,
            'hx-target' => $target,
            'hx-push-url' => 'true',
            'type' => 'button',
            'variant' => $variant
                ? $variant->value
                : Variant::DEFAULT->value,
        ];

        if ($size) {
            $attributes['size'] = $size->value;
        }

        if ($style) {
            $attributes[$style->toAttributeName()] = true;
        }

        return sprintf(
            '%s%s%s',
            $this->openHtmlTag('sl-button', $attributes),
            $content,
            $this->closeHtmlTag('sl-button'),
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
