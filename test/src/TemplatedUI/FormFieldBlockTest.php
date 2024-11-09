<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\TemplatedUI;

use ActiveCollab\Retro\TemplatedUI\ComponentIdResolver\ComponentIdResolverInterface;
use ActiveCollab\Retro\TemplatedUI\Form\FormFieldBlock;
use ActiveCollab\Retro\TemplatedUI\Input\InputTextTag;
use ActiveCollab\Retro\Test\Base\TestCase;

class FormFieldBlockTest extends TestCase
{
    public function testWillInjectIdIntoField(): void
    {
        $componentIdResolver = $this->createMock(ComponentIdResolverInterface::class);
        $componentIdResolver
            ->expects($this->once())
            ->method('getUniqueId')
            ->willReturn('unique-id');

        $this->assertStringContainsString(
            'sl-input id="unique-id"',
            (new FormFieldBlock($componentIdResolver))->render(
                'example_field',
                (new InputTextTag($componentIdResolver))->render('example_field'),
            ),
        );
    }

    public function testWillInjectLabelIntoField(): void
    {
        $componentIdResolver = $this->createMock(ComponentIdResolverInterface::class);

        $this->assertStringContainsString(
            'label="This is a label"',
            (new FormFieldBlock($componentIdResolver))->render(
                'example_field',
                (new InputTextTag($componentIdResolver))->render('example_field'),
                label: 'This is a label',
            ),
        );
    }

    public function testWillInjectRequiredIntoField(): void
    {
        $componentIdResolver = $this->createMock(ComponentIdResolverInterface::class);

        $this->assertStringContainsString(
            ' required ',
            (new FormFieldBlock($componentIdResolver))->render(
                'example_field',
                (new InputTextTag($componentIdResolver))->render('example_field'),
                required: true,
            ),
        );
    }
}
