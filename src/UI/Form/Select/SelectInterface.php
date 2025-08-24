<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Form\Select;

use ActiveCollab\Retro\UI\Common\Property\WithExplainerInterface;
use ActiveCollab\Retro\UI\Common\Property\WithInjectedAttributesInterface;
use ActiveCollab\Retro\UI\Common\Property\WithLabelInterface;
use ActiveCollab\Retro\UI\Common\Property\WithNameInterface;
use ActiveCollab\Retro\UI\Common\Property\WithPlaceholderInterface;
use ActiveCollab\Retro\UI\Common\Property\WithSizeInterface;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;
use ActiveCollab\Retro\UI\Form\Select\Element\ElementInterface;

interface SelectInterface extends RenderableElementInterface, WithNameInterface, WithLabelInterface, WithPlaceholderInterface, WithExplainerInterface, WithSizeInterface, WithInjectedAttributesInterface
{
    public function getValue(): mixed;
    public function getPreRenderedElement(): ?PreRenderedElementInterface;

    /**
     * @return ElementInterface[]
     */
    public function getElements(): array;
}
