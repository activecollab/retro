<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\AppBootstrapper\Web;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ActiveCollab\Retro\Bootstrapper\AppBootstrapper\AppBootstrapperInterface;

interface WebAppBootstrapperInterface extends AppBootstrapperInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface;
}
