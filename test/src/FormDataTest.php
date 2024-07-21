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
        $this->assertSame(
            'this is a string value',
            (new FormData($this->createMock(LoggerInterface::class)))->extractStringFromRequest(
                $this->prepareRequest(
                    [
                        'field_name' => 'this is a string value',
                    ]
                ),
                'field_name',
            ),
        );
    }

    public function testWillExtractTrimmedString(): void
    {
        $this->assertSame(
            'this is a string value',
            (new FormData($this->createMock(LoggerInterface::class)))->extractTrimmedStringFromRequest(
                $this->prepareRequest(
                    [
                        'field_name' => '    this is a string value   ',
                    ]
                ),
                'field_name',
            ),
        );
    }

    /**
     * @dataProvider provideValuesForIntExtraction
     */
    public function testWillExtractIntFromRequest(
        mixed $rawInputValue,
        int $expectedValue,
    )
    {
        $this->assertSame(
            $expectedValue,
            (new FormData($this->createMock(LoggerInterface::class)))->extractIntFromRequest(
                $this->prepareRequest(
                    [
                        'field_name' => $rawInputValue,
                    ]
                ),
                'field_name',
            ),
        );
    }

    public function provideValuesForIntExtraction(): array
    {
        return [
            ['123', 123],
            [123, 123],
        ];
    }

    /**
     * @dataProvider provideValuesForFloatExtraction
     */
    public function testWillExtractFloatFromRequest(
        mixed $rawInputValue,
        float $expectedValue,
    )
    {
        $this->assertSame(
            $expectedValue,
            (new FormData($this->createMock(LoggerInterface::class)))->extractFloatFromRequest(
                $this->prepareRequest(
                    [
                        'field_name' => $rawInputValue,
                    ]
                ),
                'field_name',
            ),
        );
    }

    public function provideValuesForFloatExtraction(): array
    {
        return [
            ['123', 123.0],
            ['123.4', 123.4],
            [123.5, 123.5],
        ];
    }

    public function testWillReturnNullWhenValueIsOptional(): void
    {
        $this->assertNull(
            (new FormData($this->createMock(LoggerInterface::class)))->extractOptionalIntFromRequest(
                $this->prepareRequest([]),
                'field_name',
            ),
        );

        $this->assertNull(
            (new FormData($this->createMock(LoggerInterface::class)))->extractOptionalFloatFromRequest(
                $this->prepareRequest([]),
                'field_name',
            ),
        );
    }

    public function testWillExtractArrayFromRequest(): void
    {
        $this->assertSame(
            [
                '1',
                '2',
                '3',
            ],
            (new FormData($this->createMock(LoggerInterface::class)))->extractArrayFromRequest(
                $this->prepareRequest(
                    [
                        'field_name' => [
                            '1',
                            '2',
                            '3',
                        ],
                    ]
                ),
                'field_name',
            ),
        );
    }

    public function testWillExtractArrayOfIdsFromRequest(): void
    {
        $this->assertSame(
            [
                1,
                2,
                3,
            ],
            (new FormData($this->createMock(LoggerInterface::class)))->extractArrayOfIdsFromRequest(
                $this->prepareRequest(
                    [
                        'field_name' => [
                            '1',
                            2,
                            '3',
                        ],
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
