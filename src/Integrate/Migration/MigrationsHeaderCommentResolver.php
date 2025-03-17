<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Integrate\Migration;

class MigrationsHeaderCommentResolver implements MigrationsHeaderCommentResolverInterface
{
    public function __construct(
        private string $migrations_header_comment,
    )
    {
    }

    public function getMigrationsHeaderComment(): string
    {
        return $this->migrations_header_comment;
    }
}
