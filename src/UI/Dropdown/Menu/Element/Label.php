<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Dropdown\Menu\Element;

use ActiveCollab\TemplatedUI\Helper\HtmlHelpersTrait;

class Label extends MenuElement
{
    use HtmlHelpersTrait;

    public function __construct(
        private string $label,
    )
    {
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
