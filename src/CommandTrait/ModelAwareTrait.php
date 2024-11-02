<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\CommandTrait;

use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use Symfony\Component\Console\Input\InputInterface;

trait ModelAwareTrait
{
    protected function mustGetModel(InputInterface $input): TypeInterface
    {
        return $this->get(StructureInterface::class)->getType(trim($input->getArgument('model')));
    }

    /**
     * @template TClassName
     * @param class-string<TClassName> $id
     * @return TClassName
     */
    abstract protected function get(string $id): mixed;
}
