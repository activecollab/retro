<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Select;

use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Form\Select\Element\OptionGroup;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;

class OptionGroupBlock extends WrapContentBlock
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(
        string $content,
        string $label,
    ): string
    {
        return $this->renderer->renderSelectOptionGroup(
            new OptionGroup(
                $label,
                new PreRenderedElement($content),
            ),
        );
    }
}
