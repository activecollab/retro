<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Integrate\Queue;

use Psr\Container\ContainerInterface;

interface ContainerResolverInterface
{
    public function getContainer(): ContainerInterface;
}
