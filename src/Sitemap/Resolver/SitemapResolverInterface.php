<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Sitemap\Resolver;

use ActiveCollab\Sitemap\Sitemap\SitemapInterface;

interface SitemapResolverInterface
{
    public function getSitemap(): SitemapInterface;
}
