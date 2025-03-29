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
use ActiveCollab\Retro\Integrate\Creator\CreatorInterface;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

trait ServiceContextAwareTrait
{
    private function getWithinContextOptionSettings(): array
    {
        return [
            'within-context',
            null,
            InputOption::VALUE_REQUIRED,
            'Name of the context where services should execute within.',
        ];
    }

    private function getWithoutContextOptionSettings(): array
    {
        return [
            'without-context',
            null,
            InputOption::VALUE_NONE,
            'Create service without a context. This option overrides within-context option.',
        ];
    }
    
    private function getServiceContext(InputInterface $input): ?TypeInterface
    {
        if ($input->getOption('without-context')) {
            return null;
        }

        $context = $input->getOption('within-context');

        if (empty($context)) {
            $context = $this->get(CreatorInterface::class)->getDefaultServiceContext();
        }

        if (empty($context)) {
            return null;
        }

        if (!preg_match('/^\w+$/', $context)) {
            throw new InvalidArgumentException('Context name can contain only letters, numbers and underscores.');
        }

        return $this->get(StructureInterface::class)->getType($context);
    }

    private ?string $serviceContextVariableName = null;

    private function getServiceContextVariableName(TypeInterface $serviceContext): string
    {
        if ($this->serviceContextVariableName === null) {
            $this->serviceContextVariableName = lcfirst($serviceContext->getEntityClassName());
        }

        return $this->serviceContextVariableName;
    }

    /**
     * @template TClassName
     * @param class-string<TClassName> $id
     * @return TClassName
     */
    abstract protected function get(string $id): mixed;
}
