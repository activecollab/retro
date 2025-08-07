<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Action;

use ActiveCollab\Retro\UI\Action\Trait\ConfirmTrait;
use ActiveCollab\Retro\UI\Action\Trait\SwapTrait;
use ActiveCollab\Retro\UI\Action\Trait\TargetTrait;

class MakeDeleteRequest implements ActionInterface
{
    use ConfirmTrait;
    use SwapTrait;
    use TargetTrait;

    public function __construct(
        private string $deleteUrl,
    )
    {
    }

    public function extendAttributes(array $attributes): array
    {
        $attributesToAppend = [
            'hx-delete' => $this->deleteUrl,
        ];

        $attributesToAppend = $this->applyConfirm($attributesToAppend);
        $attributesToAppend = $this->applySwap($attributesToAppend);
        $attributesToAppend = $this->applyTarget($attributesToAppend);

        return array_merge($attributes, $attributesToAppend);
    }
}
