<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Radio;

use ActiveCollab\Retro\UI\Form\Radio\Radio;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\TemplatedUI\Tag\Tag;

class RadioTag extends Tag
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(
        string $label,
        mixed $value,
        bool $disabled = false,
    ): string
    {
        return $this->renderer->renderRadio(
            new Radio(
                $label,
                $value,
                $disabled,
            ),
        );
    }
}
