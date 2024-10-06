<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Sitemap\Trait;

use ActiveCollab\DatabaseObject\Entity\EntityInterface;
use ActiveCollab\DatabaseObject\PoolInterface;
use Doctrine\Inflector\InflectorFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteInterface;

trait EntityAwareNodeMiddleware
{
    protected function getEntityFromRequest(
        ServerRequestInterface $request,
        string $entityType,
        string $routeArgumentName = null
    ): ?EntityInterface
    {
        $entityId = (int) $this
            ->getRoute($request)
            ->getArgument(
                $routeArgumentName ?? $this->getIdArgumentNameFromEntityType($entityType)
            );

        if ($entityId) {
            $entity = $this->getContainer()
                ->get(PoolInterface::class)
                ->getById($entityType, $entityId);

            if ($entity instanceof $entityType) {
                return $entity;
            }
        }

        return null;
    }

    private function getIdArgumentNameFromEntityType(string $entityType): string
    {
        $bits = explode('\\', $entityType);

        return InflectorFactory::create()->build()->tableize($bits[count($bits) - 1]) . '_id';
    }

    abstract public function &getContainer(): ?ContainerInterface;
    abstract protected function getRoute(ServerRequestInterface $request): RouteInterface;
}
