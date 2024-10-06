<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro;

use ActiveCollab\Retro\Bootstrapper\Metadata\AppNamespaceInterface;
use ActiveCollab\Retro\Bootstrapper\Metadata\PathInterface;
use ActiveCollab\Retro\Command\Command;
use Doctrine\Inflector\Inflector;
use LogicException;

abstract class RetroCommand extends Command implements RetroCommandInterface
{
    public function getCommandNamePrefix(): string
    {
        return 'retro:';
    }

    protected function getInflector(): Inflector
    {
        if (!$this->getContainer()?->has(Inflector::class)) {
            throw new LogicException('Inflector is not available.');
        }

        return $this->getContainer()->get(Inflector::class);
    }

    protected function getAppPath(): string
    {
        if (!$this->getContainer()->has(PathInterface::class)) {
            throw new LogicException('Path is not available.');
        }

        return $this->getContainer()->get(PathInterface::class)->getPath();
    }

    protected function getAppNamespace(): string
    {
        if (!$this->getContainer()->has(AppNamespaceInterface::class)) {
            throw new LogicException('App namespace is not available.');
        }

        return $this->getContainer()->get(AppNamespaceInterface::class)->getNamespace();
    }
}
