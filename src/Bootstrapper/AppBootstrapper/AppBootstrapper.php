<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\AppBootstrapper;

use ActiveCollab\Retro\Bootstrapper\Metadata\MetadataInterface;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

abstract class AppBootstrapper implements AppBootstrapperInterface
{
    private bool $isBootstrapped = false;
    private bool $isRan = false;

    public function __construct(
        private MetadataInterface $appMetadata,
        private ContainerInterface $container,
        protected LoggerInterface $logger,
    )
    {
    }

    public function getAppMetadata(): MetadataInterface
    {
        return $this->appMetadata;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function isBootstrapped(): bool
    {
        return $this->isBootstrapped;
    }

    protected function setIsBootstrapped(): void
    {
        $this->isBootstrapped = true;
    }

    public function isRan(): bool
    {
        return $this->isRan;
    }

    protected function setIsRan(): void
    {
        $this->isRan = true;
    }

    public function bootstrap(): AppBootstrapperInterface
    {
        if ($this->isBootstrapped()) {
            throw new LogicException('App is already bootstrapped.');
        }

        $this->logger->debug(
            'Application bootstrapped.',
            [
                'sapi' => php_sapi_name(),
            ]
        );

        return $this;
    }

    /**
     * Ran before app instance is constructed in boostrap method.
     */
    protected function beforeAppConstruction()
    {
    }

    /**
     * Ran after app instance is constructed in boostrap method.
     */
    protected function afterAppConstruction(): void
    {
    }

    public function run(bool $silent = false): AppBootstrapperInterface
    {
        if (!$this->isBootstrapped()) {
            throw new LogicException('App needs to be bootstrapped before it can be ran.');
        }

        if ($this->isRan()) {
            throw new LogicException('App is already ran.');
        }

        $this->logger->debug(
            'Application ran.',
            [
                'sapi' => php_sapi_name(),
            ],
        );

        return $this;
    }
}
