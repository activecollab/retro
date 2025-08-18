<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Surface\Details;

use ActiveCollab\Retro\UI\Common\Property\WithDisabledInterface;
use ActiveCollab\Retro\UI\Common\Property\WithRequiredLabelInterface;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;
use ActiveCollab\Retro\UI\Indicator\IconInterface;

interface DetailsInterface extends RenderableElementInterface, WithRequiredLabelInterface, WithDisabledInterface
{
    public function getContent(): PreRenderedElementInterface|string;
    public function isOpen(): bool;
    public function getExpandIcon(): ?IconInterface;
    public function getCollapseIcon(): ?IconInterface;
    public function icon(
        ?IconInterface $expandIcon,
        ?IconInterface $collapseIcon,
    ): static;
}
