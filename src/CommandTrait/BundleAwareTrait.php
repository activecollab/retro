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
use Symfony\Component\Console\Input\InputInterface;

trait BundleAwareTrait
{
    protected function mustGetBundle(InputInterface $input): BundleInterface
    {
        return $this->get(BundleManagerInterface::class)->getByName(trim($input->getArgument('bundle')));
    }

    /**
     * @template TClassName
     * @param class-string<TClassName> $id
     * @return TClassName
     */
    abstract protected function get(string $id): mixed;
}
