<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\UI\Shoelace;

use ActiveCollab\Retro\TemplatedUI\ComponentIdResolver\ComponentIdResolverInterface;
use ActiveCollab\Retro\Test\Base\TestCase;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Form\Select\Element\Option;
use ActiveCollab\Retro\UI\Form\Select\Select;
use ActiveCollab\Retro\UI\Renderer\Shoelace\ShoelaceRenderer;

class SelectTest extends TestCase
{
    private ShoelaceRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renderer = new ShoelaceRenderer(
            $this->createMock(ComponentIdResolverInterface::class),
        );
    }

    public function testWillNotAcceptPreRenderedAndOtherElements(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You cannot pass additional elements when the first argument is a pre-rendered element.');

        new Select(
            'test',
            null,
            new PreRenderedElement('Test content'),
            new Option('Option 1', 'option_1'),
        );
    }

    public function testWillRender(): void
    {
        $renderedSelect = $this->renderer->renderSelect(
            (new Select(
                'test',
                'option_1',
                new Option('Option 1', 'option_1'),
                new Option('Option 2', 'option_2'),
            ))->label('Test')->placeholder('Holding place')->explainer('Choose wisely')
        );

        $this->assertStringStartsWith('<sl-select', $renderedSelect);
        $this->assertStringContainsString('name="test"', $renderedSelect);
        $this->assertStringContainsString('label="Test"', $renderedSelect);
        $this->assertStringContainsString('value="option_1"', $renderedSelect);
        $this->assertStringContainsString('placeholder="Holding place"', $renderedSelect);
        $this->assertStringContainsString('help-text="Choose wisely"', $renderedSelect);
        $this->assertStringContainsString('<sl-option value="option_1">Option 1</sl-option>', $renderedSelect);
        $this->assertStringContainsString('<sl-option value="option_2">Option 2</sl-option>', $renderedSelect);
        $this->assertStringEndsWith('</sl-select>', $renderedSelect);
    }

    public function testWillRenderPreRenderedElement(): void
    {
        $renderedSelect = $this->renderer->renderSelect(
            new Select(
                'test',
                null,
                new PreRenderedElement('<option value="option_1">Option 1</option>'),
            )
        );

        $this->assertStringStartsWith('<sl-select', $renderedSelect);
        $this->assertStringContainsString('name="test"', $renderedSelect);
        $this->assertStringContainsString('<option value="option_1">Option 1</option>', $renderedSelect);
        $this->assertStringEndsWith('</sl-select>', $renderedSelect);
    }
}
