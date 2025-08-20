<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Surface\Drawer;

use ActiveCollab\Retro\UI\Common\Property\WithIdInterface;
use ActiveCollab\Retro\UI\Common\Property\WithRequiredLabelInterface;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElementInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface DrawerInterface extends RenderableElementInterface, WithRequiredLabelInterface, WithIdInterface
{
    public function getContent(): PreRenderedElementInterface|string;
}
