<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuItem;

use ActiveCollab\Retro\UI\Action\ActionInterface;
use ActiveCollab\Retro\UI\Common\AdornmentInterface;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithAdornmentsTrait;
use ActiveCollab\Retro\UI\Common\Property\Trait\WithRequiredLabelTrait;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuElement;
use ActiveCollab\TemplatedUI\Helper\HtmlHelpersTrait;

class MenuItem extends MenuElement implements MenuItemInterface
{
    use HtmlHelpersTrait;
    use WithRequiredLabelTrait;
    use WithAdornmentsTrait;

    public function __construct(
        string $label,
        private ?ActionInterface $action = null,
        ?AdornmentInterface $leftAdornment = null,
        ?AdornmentInterface $rightAdornment = null,
    )
    {
        $this->label = $label;
        $this->leftAdornment = $leftAdornment;
        $this->rightAdornment = $rightAdornment;
    }

    public function getAction(): ?ActionInterface
    {
        return $this->action;
    }
}
