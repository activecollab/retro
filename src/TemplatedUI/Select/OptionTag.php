<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Select;

use ActiveCollab\Retro\UI\Form\Select\Element\Option;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\TemplatedUI\Tag\Tag;

class OptionTag extends Tag
{
    public function __construct(
        private RendererInterface $renderer,
    )
    {
    }

    public function render(string $label, mixed $value): string
    {
        return $this->renderer->renderSelectOption(
            new Option(
                $label,
                $value,
            ),
        );
    }
}
