<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Dropdown;

use ActiveCollab\Retro\UI\Dropdown\Common\PanelInterface;
use ActiveCollab\Retro\UI\Dropdown\Common\TriggerInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface DropdownInterface extends RenderableElementInterface
{
    public function getTrigger(): TriggerInterface|RenderableElementInterface;
    public function getPanel(): PanelInterface|RenderableElementInterface;
}
