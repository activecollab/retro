<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Indicator;

use ActiveCollab\TemplatedUI\Tag\Tag;

class ProgressBarTag extends Tag
{
    public function render(
        int $value,
        ?string $title = null,
    ): string
    {
        if ($value < 0 || $value > 100) {
            throw new \InvalidArgumentException('Value must be between 0 and 100.');
        }

        $attributes = [
            'value' => $value,
        ];

        if ($title) {
            $attributes['title'] = $title;
        }

        return sprintf(
            '%s%s',
            $this->openHtmlTag(
                'sl-progress-bar',
                $attributes,
            ),
            $this->closeHtmlTag('sl-progress-bar'),
        );
    }
}