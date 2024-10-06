<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Sitemap\PathResolver;

class SitemapPathResolver implements SitemapPathResolverInterface
{
    private string $sitemap_path;

    public function __construct(string $sitemap_path)
    {
        $this->sitemap_path = $sitemap_path;
    }

    public function getSitemapPath(): string
    {
        return $this->sitemap_path;
    }
}
