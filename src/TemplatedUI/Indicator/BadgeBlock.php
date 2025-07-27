<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Indicator;

use ActiveCollab\TemplatedUI\Helper\HtmlHelpersTrait;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class BadgeBlock extends WrapContentBlock
{
    use HtmlHelpersTrait;

    public function render(
        string $content,
    ): string
    {
        return sprintf(
            '%s%s%s',
            $this->openHtmlTag('sl-badge'),
            $content,
            $this->closeHtmlTag('sl-badge'),
        );
    }
}
