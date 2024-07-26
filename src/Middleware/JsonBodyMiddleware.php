<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Middleware;

use ActiveCollab\Retro\Middleware\Exception\MalformedRequestBodyException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonBodyMiddleware implements MiddlewareInterface
{
    private array $nonBodyRequests = [
        'GET',
        'HEAD',
        'OPTIONS',
    ];

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        if (in_array($request->getMethod(), $this->nonBodyRequests)) {
            return $handler->handle($request);
        }

        $contentType = $request->getHeaderLine('Content-Type');

        if (preg_match('#^application/(|[\S]+\+)json($|[ ;])#', $contentType)) {
            return $handler->handle($this->parse($request));
        }

        return $handler->handle($request);
    }

    public function parse(ServerRequestInterface $request): ServerRequestInterface
    {
        $rawBody = (string) $request->getBody();

        if (empty($rawBody)) {
            return $request->withParsedBody(null);
        }

        $parsedBody = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new MalformedRequestBodyException(
                sprintf(
                    'Error when parsing JSON request body: %s',
                    json_last_error_msg()
                )
            );
        }

        if (!is_array($parsedBody)) {
            $parsedBody = null;
        }

        return $request->withParsedBody($parsedBody);
    }
}
