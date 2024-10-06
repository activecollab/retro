<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\CommandTrait;

use ActiveCollab\Retro\Bootstrapper\Bundle\BundleInterface;
use ActiveCollab\Retro\Bootstrapper\Bundle\Manager\BundleManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;

trait BundleAwareTrait
{
    protected function mustGetBundle(InputInterface $input): BundleInterface
    {
        /** @var BundleManagerInterface $bundleManager */
        $bundleManager = $this->getContainer()->get(BundleManagerInterface::class);

        return $bundleManager->getByName(trim($input->getArgument('bundle')));
    }

    abstract public function &getContainer(): ?ContainerInterface;
}
