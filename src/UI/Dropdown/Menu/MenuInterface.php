<?php

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Dropdown\Menu;

use ActiveCollab\Retro\UI\Dropdown\Common\PanelInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuElementInterface;
use ActiveCollab\Retro\UI\Element\RenderableElementInterface;

interface MenuInterface extends PanelInterface, RenderableElementInterface
{
    /**
     * @return MenuElementInterface[]
     */
    public function getElements(): array;
}
