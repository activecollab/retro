<?php

namespace ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuItem;

use ActiveCollab\Retro\UI\Action\ActionInterface;
use ActiveCollab\Retro\UI\Common\AdornmentInterface;
use ActiveCollab\Retro\UI\Common\Property\WithAdornmentsInterface;
use ActiveCollab\Retro\UI\Common\Property\WithRequiredLabelInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuElementInterface;

interface MenuItemInterface extends MenuElementInterface, WithRequiredLabelInterface, WithAdornmentsInterface
{
    public function getLabel(): string;
    public function getAction(): ?ActionInterface;
}