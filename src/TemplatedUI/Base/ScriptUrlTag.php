<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Base;

use ActiveCollab\TemplatedUI\Integrate\PathsResolverInterface;
use ActiveCollab\TemplatedUI\Tag\Tag;

class ScriptUrlTag extends Tag
{
    public function __construct(
        private PathsResolverInterface $pathsResolver,
    )
    {
    }

    public function render(string $scriptName = 'application.js'): string
    {
        return sprintf(
            '%s/%s?%d',
            $this->pathsResolver->getAssetsUrl(),
            $scriptName,
            $this->pathsResolver->getAssetTimestamp($scriptName) ?? time(),
        );
    }
}
