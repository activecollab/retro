<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\AppBootstrapper\Web;

use DI\Bridge\Slim\Bridge;
use LogicException;
use ActiveCollab\Retro\Bootstrapper\AppBootstrapper\AppBootstrapper;
use ActiveCollab\Retro\Bootstrapper\AppBootstrapper\AppBootstrapperInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App as SlimApp;

abstract class SlimAppBootstrapper extends AppBootstrapper implements WebAppBootstrapperInterface
{
    private ?SlimApp $app = null;

    public function getApp(): SlimApp
    {
        if (empty($this->app)) {
            throw new LogicException('App not set up.');
        }

        return $this->app;
    }

    public function bootstrap(): AppBootstrapperInterface
    {
        parent::bootstrap();

        $this->beforeAppConstruction();

        $this->app = Bridge::create($this->getContainer());
        $this->app->addRoutingMiddleware();

        $this->afterAppConstruction();

        $this->app->options(
            '/{routes:.+}',
            function ($request, $response, $args) {
                return $response;
            },
        );

        $this->app->add(
            function ($request, $handler) {
                $response = $handler->handle($request);

                return $response
                    ->withHeader('Access-Control-Allow-Origin', '*')
                    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
            },
        );

        $this->setIsBootstrapped();

        return $this;
    }

    public function run(bool $silent = false): AppBootstrapperInterface
    {
        parent::run($silent);

        $this->app->run();
        $this->setIsRan();

        return $this;
    }

    public function handle(
        ServerRequestInterface $request
    ): ResponseInterface
    {
        if (!$this->isBootstrapped()) {
            throw new LogicException('App needs to be bootstrapped before it can be ran.');
        }

        $this->logger->info(
            'Handling {method} request to {uri}.',
            [
                'method' => $request->getMethod(),
                'uri' => (string) $request->getUri(),
                'headers' => $request->getHeaders(),
            ],
        );

        return $this->app->handle($request);
    }
}
