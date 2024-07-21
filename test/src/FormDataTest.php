<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test;

use ActiveCollab\Retro\FormData\FormData;
use ActiveCollab\Retro\Test\Base\TestCase;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class FormDataTest extends TestCase
{
    public function testWillExtractStringFromRequest(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $this->assertSame(
            'this is a string value',
            (new FormData($logger))->extractStringFromRequest(
                $this->prepareRequest(
                    [
                        'field_name' => 'this is a string value',
                    ]
                ),
                'field_name',
            ),
        );
    }

    private function prepareRequest(mixed $payload): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest(
            'POST',
            'http://example.com', [], 'this is a string value'
        )->withParsedBody($payload);
    }
}
