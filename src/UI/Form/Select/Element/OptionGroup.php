<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Form\Select\Element;

use ActiveCollab\Retro\UI\Common\Property\Trait\WithRequiredLabelTrait;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;
use LogicException;

class OptionGroup implements OptionGroupInterface
{
    use WithRequiredLabelTrait;

    private ?PreRenderedElementInterface $preRenderedElement = null;
    private array $options = [];

    public function __construct(
        string $label,
        PreRenderedElementInterface|OptionInterface $firstOptionOrPreRenderedElement,
        OptionInterface ...$additionalOptions,
    )
    {
        $this->label = $label;

        if ($firstOptionOrPreRenderedElement instanceof PreRenderedElementInterface) {
            $this->preRenderedElement = $firstOptionOrPreRenderedElement;

            if (!empty($additionalOptions)) {
                throw new LogicException('You cannot pass additional options when the first argument is a pre-rendered element.');
            }
        } else {
            $this->options[] = $firstOptionOrPreRenderedElement;

            if (!empty($additionalOptions)) {
                $this->options = array_merge(
                    $this->options,
                    $additionalOptions,
                );
            }
        }
    }

    public function getPreRenderedElement(): ?PreRenderedElementInterface
    {
        return $this->preRenderedElement;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderSelectOptionGroup($this, ...$extensions);
    }
}
