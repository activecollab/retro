<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuItem;

use ActiveCollab\Retro\UI\Adornment\AdornmentInterface;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuElement;
use ActiveCollab\Retro\UI\Dropdown\Menu\MenuInterface;
use ActiveCollab\TemplatedUI\Helper\HtmlHelpersTrait;

class MenuItem extends MenuElement
{
    use HtmlHelpersTrait;

    public function __construct(
        private string $label,
        private ?AdornmentInterface $prefix = null,
        private ?AdornmentInterface $suffix = null,
        private ?MenuInterface $submenu = null,
    )
    {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getPrefix(): ?AdornmentInterface
    {
        return $this->prefix;
    }

    public function getSuffix(): ?AdornmentInterface
    {
        return $this->suffix;
    }

    public function getSubmenu(): ?MenuInterface
    {
        return $this->submenu;
    }
}
