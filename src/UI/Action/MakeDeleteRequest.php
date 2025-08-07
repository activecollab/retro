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
        private ?string $target = null,
        private ?string $swap = null,
        private ?string $confirmMessage = null,
    )
    {
    }

    public function extendAttributes(array $attributes): array
    {
        $attributesToAppend = [
            'hx-delete' => $this->deleteUrl,
        ];

        if ($this->target) {
            $attributesToAppend['hx-target'] = $this->target;
        }

        if ($this->swap) {
            $attributesToAppend['hx-swap'] = $this->swap;
        }

        if ($this->confirmMessage) {
            $attributesToAppend['hx-confirm'] =  $this->confirmMessage;
        }

        return array_merge($attributes, $attributesToAppend);
    }
}
