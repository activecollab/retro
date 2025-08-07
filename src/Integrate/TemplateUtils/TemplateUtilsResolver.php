<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Integrate\TemplateUtils;

use ActiveCollab\TemplatedUI\Util\TemplateUtilsResolverInterface;
use Psr\Container\ContainerInterface;

class TemplateUtilsResolver implements TemplateUtilsResolverInterface
{
    public function __construct(
        private ContainerInterface $container,
        private array $utilServiceMap = [],
    )
    {
    }

    public function resolve(): array
    {
        $result = [];

        foreach ($this->utilServiceMap as $templateVar => $serviceId) {
            $result[$templateVar] = $this->container->get($serviceId);
        }

        return $result;
    }
}
