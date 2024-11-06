<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\ComponentIdResolver;

class ComponentIdResolver implements ComponentIdResolverInterface
{
    private array $ids = [];

    public function getUniqueId(
        string $prefix = null,
        bool $variableNameSafe = true,
    ): string
    {
        do {
            $id = trim(
                sprintf(
                    '%s_%s',
                    $prefix ?? 'cmp',
                    $this->generate()
                ),
                ' _'
            );
        } while (in_array($id, $this->ids));

        return $id;
    }

    public function generate(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        $max = mb_strlen(
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
            '8bit',
        ) - 1;

        $result = '';

        for ($i = 0; $i < 40; ++$i) {
            $result .= $characters[random_int(0, $max)];
        }

        return $result;
    }
}
