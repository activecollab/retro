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
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;

trait ModelAwareTrait
{
    protected function mustGetModel(InputInterface $input): TypeInterface
    {
        /** @var StructureInterface $structure */
        $structure = $this->getContainer()->get(StructureInterface::class);

        return $structure->getType(trim($input->getArgument('model')));
    }

    abstract public function &getContainer() : ? ContainerInterface;
}
