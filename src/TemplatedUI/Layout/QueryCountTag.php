<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Layout;

use ActiveCollab\DatabaseConnection\QueryLogger\QueryLoggerInterface;
use ActiveCollab\TemplatedUI\Tag\Tag;

class QueryCountTag extends Tag
{
    public function render(): string
    {
        return (string) $this->getContainer()
            ->get(QueryLoggerInterface::class)
                ->getNumberOfQueries();
    }
}
