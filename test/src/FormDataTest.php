<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test;

use ActiveCollab\PhoneNumber\Factory\PhoneNumberFactoryInterface;
use ActiveCollab\Retro\FormData\FormData;
use ActiveCollab\Retro\Test\Base\TestCase;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class FormDataTest extends TestCase
{
    public function testWillExtractStringFromRequest(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $phoneNumberFactory = $this->createMock(PhoneNumberFactoryInterface::class);

        $this->assertSame(
            'this is a string value',
            (new FormData($logger, $phoneNumberFactory))->extractStringFromRequest(
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
        $logger = $this->createMock(LoggerInterface::class);
        $phoneNumberFactory = $this->createMock(PhoneNumberFactoryInterface::class);

        $this->assertSame(
            'this is a string value',
            (new FormData($logger, $phoneNumberFactory))->extractTrimmedStringFromRequest(
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
        $logger = $this->createMock(LoggerInterface::class);
        $phoneNumberFactory = $this->createMock(PhoneNumberFactoryInterface::class);

        $this->assertSame(
            $expectedValue,
            (new FormData($logger, $phoneNumberFactory))->extractIntFromRequest(
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
        $logger = $this->createMock(LoggerInterface::class);
        $phoneNumberFactory = $this->createMock(PhoneNumberFactoryInterface::class);

        $this->assertSame(
            $expectedValue,
            (new FormData($logger, $phoneNumberFactory))->extractFloatFromRequest(
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
        $logger = $this->createMock(LoggerInterface::class);
        $phoneNumberFactory = $this->createMock(PhoneNumberFactoryInterface::class);

        $this->assertNull(
            (new FormData($logger, $phoneNumberFactory))->extractOptionalIntFromRequest(
                $this->prepareRequest([]),
                'field_name',
            ),
        );

        $this->assertNull(
            (new FormData($logger, $phoneNumberFactory))->extractOptionalFloatFromRequest(
                $this->prepareRequest([]),
                'field_name',
            ),
        );
    }

    public function testWillExtractArrayFromRequest(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $phoneNumberFactory = $this->createMock(PhoneNumberFactoryInterface::class);

        $this->assertSame(
            [
                '1',
                '2',
                '3',
            ],
            (new FormData($logger, $phoneNumberFactory))->extractArrayFromRequest(
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
        $logger = $this->createMock(LoggerInterface::class);
        $phoneNumberFactory = $this->createMock(PhoneNumberFactoryInterface::class);

        $this->assertSame(
            [
                1,
                2,
                3,
            ],
            (new FormData($logger, $phoneNumberFactory))->extractArrayOfIdsFromRequest(
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
            'https://example.com',
        )->withParsedBody($payload);
    }
}
