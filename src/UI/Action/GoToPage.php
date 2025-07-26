<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Action;

class GoToPage implements ActionInterface
{
    public function __construct(
        private string $pageUrl,
    )
    {
    }

    public function extendAttributes(array $attributes): array
    {
        return array_merge(
            $attributes,
            [
                'hx-get' => $this->pageUrl,
                'hx-target' => 'body',
                'hx-push-url' => 'true',
            ],
        );
    }
}
