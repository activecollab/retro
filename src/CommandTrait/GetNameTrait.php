<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\CommandTrait;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

trait GetNameTrait
{
    private function addRequiredNameArgument(string $argumentName, string $description): static
    {
        return $this->addArgument($argumentName, InputArgument::REQUIRED, $description);
    }

    private function mustGetName(string $argumentName, InputInterface $input): string
    {
        $fieldName = trim($input->getArgument($argumentName));

        if (empty($fieldName)) {
            throw new InvalidArgumentException('Name is required.');
        }

        return $fieldName;
    }
}
