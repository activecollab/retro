<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Form\Select;

use ActiveCollab\Retro\UI\Common\Property\Trait\WithExplainerTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithInjectedAttributesTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithLabelTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithNameTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithPlaceholderTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithSizeTrait;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Form\Select\Element\ElementInterface;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\RenderingExtensionInterface;
use LogicException;

class Select implements SelectInterface
{
    use WithNameTrait;
    use WithLabelTrait;
    use WithPlaceholderTrait;
    use WithExplainerTrait;
    use WithSizeTrait;
    use WithInjectedAttributesTrait;

    private ?PreRenderedElementInterface $preRenderedElement = null;
    private array $elements = [];

    public function __construct(
        string $name,
        private mixed $value,
        PreRenderedElementInterface|ElementInterface $firstOrPreRenderedElement,
        ElementInterface ...$additionalElements,
    )
    {
        $this->name = $name;

        if ($firstOrPreRenderedElement instanceof PreRenderedElementInterface) {
            $this->preRenderedElement = $firstOrPreRenderedElement;

            if (!empty($additionalElements)) {
                throw new LogicException('You cannot pass additional elements when the first argument is a pre-rendered element.');
            }
        } else {
            $this->elements[] = $firstOrPreRenderedElement;

            if (!empty($additionalElements)) {
                $this->elements = array_merge(
                    $this->elements,
                    $additionalElements,
                );
            }
        }
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getPreRenderedElement(): ?PreRenderedElementInterface
    {
        return $this->preRenderedElement;
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    public function renderUsingRenderer(
        RendererInterface $renderer,
        RenderingExtensionInterface ...$extensions,
    ): string
    {
        return $renderer->renderSelect(
            $this,
            ...$extensions,
        );
    }
}
