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
use ActiveCollab\Retro\UI\Action\ActionInterface;
use ActiveCollab\Retro\UI\Action\GoToPage;
use ActiveCollab\Retro\UI\Button\Button;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\TemplatedUI\MethodInvoker\CatchAllParameters\CatchAllParametersInterface;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;
use LogicException;

class ButtonBlock extends WrapContentBlock implements ButtonBlockInterface
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(
        string $content,
        ?ActionInterface $action = null,
        ?ButtonVariant $variant = null,
        ?ButtonStyle $style = null,
        ?Size $size = null,
        ?Width $width = null,
        ?CatchAllParametersInterface $catchAllParameters = null,
    ): string
    {
        return (new Button(
            new PreRenderedElement($content),
            action: $this->getButtonAction($action, $catchAllParameters),
            type: $catchAllParameters
                ? $this->getButtonType($catchAllParameters)
                : 'button',
            variant: $variant,
            style: $style,
            size: $size,
            width: $width,
        ))->renderUsingRenderer($this->renderer);
    }

    private function getButtonAction(
        ?ActionInterface $action,
        ?CatchAllParametersInterface $catchAllParameters,
    ): ?ActionInterface
    {
        if ($action) {
            return $action;
        }

        if ($catchAllParameters && $catchAllParameters->hasParameter('href')) {
            return new GoToPage($catchAllParameters->getParameter('href'));
        }

        return null;
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
