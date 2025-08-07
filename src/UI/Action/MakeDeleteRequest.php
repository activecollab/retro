<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Action;

class MakeDeleteRequest implements ActionInterface
{
    public function __construct(
        private string $deleteUrl,
        private ?string $confirmMessage = 'Are you sure you want to delete this item?',
    )
    {
    }

    public function extendAttributes(array $attributes): array
    {
        $attributesToAppend = [
            'hx-delete' => $this->deleteUrl,
        ];

        if ($this->confirmMessage) {
            $attributesToAppend['hx-confirm'] =  $this->confirmMessage;
        }

        return array_merge($attributes, $attributesToAppend);
    }
}
