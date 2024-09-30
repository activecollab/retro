<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\Metadata;

use InvalidArgumentException;

class Environment implements EnvironmentInterface
{
    private string $environment;

    public function __construct(string $environment)
    {
        if (!$this->isValidEnvironment($environment)) {
            throw new InvalidArgumentException(
                sprintf("Value '%s' is not a supported environment.", $environment)
            );
        }

        $this->environment = $environment;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    private function isValidEnvironment(string $environment): bool
    {
        return in_array($environment, self::VALID_ENVIRONMENTS);
    }

    public function isProduction(): bool
    {
        return $this->environment === self::PRODUCTION;
    }

    public function isDevelopment(): bool
    {
        return $this->environment === self::DEVELOPMENT;
    }

    public function isTest(): bool
    {
        return $this->environment === self::TEST;
    }

    public function __toString()
    {
        return $this->getEnvironment();
    }
}
