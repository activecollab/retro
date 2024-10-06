<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Sitemap\Resolver;

use ActiveCollab\Sitemap\Sitemap\SitemapInterface;
use Psr\Container\ContainerInterface;

class SitemapResolver implements SitemapResolverInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSitemap(): SitemapInterface
    {
        return $this->container->get(SitemapInterface::class);
    }
}
