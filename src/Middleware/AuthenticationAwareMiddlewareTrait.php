<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Middleware;

use ActiveCollab\User\UserInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

trait AuthenticationAwareMiddlewareTrait
{
    private function getAuthenticatedUser(
        ServerRequestInterface $request,
        string $attributeName = 'authenticated_user',
    ): ?UserInterface
    {
        return $request->getAttribute($attributeName);
    }

    private function mustGetAuthenticatedUser(ServerRequestInterface $request): UserInterface
    {
        $user = $this->getAuthenticatedUser($request);

        if (!$user instanceof UserInterface) {
            throw new RuntimeException('Authenticated user not found.');
        }

        return $user;
    }
}
