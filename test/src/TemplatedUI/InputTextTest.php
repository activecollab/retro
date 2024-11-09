<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\TemplatedUI;

use ActiveCollab\PhoneNumber\Factory\PhoneNumberFactoryInterface;
use ActiveCollab\Retro\FormData\FormData;
use ActiveCollab\Retro\TemplatedUI\ComponentIdResolver\ComponentIdResolverInterface;
use ActiveCollab\Retro\TemplatedUI\Input\InputTextTag;
use ActiveCollab\Retro\Test\Base\TestCase;
use Psr\Log\LoggerInterface;

class InputTextTest extends TestCase
{
    public function testWillUseValueFromFormData(): void
    {
        $componentIdResolver = $this->createMock(ComponentIdResolverInterface::class);

        $this->assertStringContainsString(
            'value="Text view form data"',
            (new InputTextTag($componentIdResolver))->render(
                'example_field',
                formData: new FormData(
                    $this->createMock(LoggerInterface::class),
                    $this->createMock(PhoneNumberFactoryInterface::class),
                    [
                        'example_field' => 'Text view form data',
                    ],
                ),
            ),
        );
    }
}
