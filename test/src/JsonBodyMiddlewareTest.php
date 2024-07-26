<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test;

use ActiveCollab\Retro\Middleware\Exception\MalformedRequestBodyException;
use ActiveCollab\Retro\Middleware\JsonBodyMiddleware;
use ActiveCollab\Retro\Test\Base\TestCase;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonBodyMiddlewareTest extends TestCase
{
    /**
     * @dataProvider provideNonBodyMethods
     */
    public function testWillSkipNonBodyRequests(string $nonBodyMethod): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest($nonBodyMethod, 'https://example,com')
            ->withHeader('Content-Type', 'application/json')
            ->withBody((new StreamFactory())->createStream('{"foo":"bar"}'));

        $handler = $this->getHandler();

        (new JsonBodyMiddleware())->process($request, $handler);

        $this->assertNull($handler->getLastRequest()->getParsedBody());
    }

    public function provideNonBodyMethods(): array
    {
        return [
            ['GET'],
            ['HEAD'],
            ['OPTIONS'],
        ];
    }

    public function testWillSkipNonJsonRequests(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', 'https://example,com')
            ->withBody((new StreamFactory())->createStream('{"foo":"bar"}'));


        $handler = $this->getHandler();

        (new JsonBodyMiddleware())->process($request, $handler);

        $this->assertNull($handler->getLastRequest()->getParsedBody());
    }

    public function testWillRejectInvalidJsonPayload(): void
    {
        $this->expectException(MalformedRequestBodyException::class);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', 'https://example,com')
            ->withHeader('Content-Type', 'application/json')
            ->withBody((new StreamFactory())->createStream('invalid JSON'));

        (new JsonBodyMiddleware())->process($request, $this->getHandler());
    }

    public function testWillParseJson(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', 'https://example,com')
            ->withHeader('Content-Type', 'application/json')
            ->withBody((new StreamFactory())->createStream('{"foo":"bar"}'));

        $handler = $this->getHandler();

        (new JsonBodyMiddleware())->process($request, $handler);

        $this->assertSame(
            [
                'foo' => 'bar',
            ],
            $handler->getLastRequest()->getParsedBody(),
        );
    }

    private function getHandler(): RequestHandlerInterface
    {
        return new class implements RequestHandlerInterface
        {
            private ServerRequestInterface $lastRequest;

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->lastRequest = $request;

                return (new ResponseFactory())->createResponse();
            }

            public function getLastRequest(): ServerRequestInterface
            {
                return $this->lastRequest;
            }
        };
    }
}