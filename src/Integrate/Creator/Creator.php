<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Integrate\Creator;

use ActiveCollab\Retro\Bootstrapper\Bundle\BundleInterface;
use ActiveCollab\Retro\Service\Service;
use ActiveCollab\Retro\Service\ServiceInterface;

abstract class Creator implements CreatorInterface
{
    public function __construct(
        private string $modelNamespace,
        private string $bundlesNamespace,
    )
    {
    }

    public function getModelNamespace(): string
    {
        return $this->modelNamespace;
    }

    public function getBundlesNamespace(): string
    {
        return $this->bundlesNamespace;
    }

    public function getBundleNamespace(BundleInterface $bundle): string
    {
        return sprintf(
            '%s\\%s',
            $this->getBundlesNamespace(),
            $bundle->getName(),
        );
    }

    public function getBaseService(bool $interface = false): string
    {
        return $interface ? ServiceInterface::class : Service::class;
    }
}
