<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\ContainerProxy;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContainerProxy implements ContainerInterface
{
    private array $aliases = [];

    public function __construct(
        private ContainerInterface $container,
        array $aliases = [],
    )
    {
        foreach ($aliases as $key => $alias) {
            if (is_int($key)) {
                $this->aliases[$alias] = $alias;
                continue;
            }

            $this->aliases[$key] = $alias;
        }
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new class(
                sprintf(
                    "Service '%s' not found in container",
                    $id,
                ),
            ) extends Exception implements NotFoundExceptionInterface
            {
            };
        }

        return $this->container->get($this->aliases[$id]);
    }

    public function has(string $id)
    {
        return array_key_exists($id, $this->aliases)
            && $this->container->has($this->aliases[$id]);
    }
}
