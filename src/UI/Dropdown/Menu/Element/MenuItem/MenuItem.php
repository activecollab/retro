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
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuElement;
use ActiveCollab\Retro\UI\Dropdown\Menu\MenuInterface;
use ActiveCollab\TemplatedUI\Helper\HtmlHelpersTrait;

class MenuItem extends MenuElement
{
    use HtmlHelpersTrait;

    public function __construct(
        private string $label,
        private ?ActionInterface $action = null,
        private ?AdornmentInterface $leftAdornment = null,
        private ?AdornmentInterface $rightAdornment = null,
    )
    {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getAction(): ?ActionInterface
    {
        return $this->action;
    }

    public function getLeftAdornment(): ?AdornmentInterface
    {
        return $this->leftAdornment;
    }

    public function getRightAdornment(): ?AdornmentInterface
    {
        return $this->rightAdornment;
    }
}
