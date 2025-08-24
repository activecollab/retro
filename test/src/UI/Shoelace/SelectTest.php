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
use ActiveCollab\Retro\UI\Form\Select\Element\OptionGroup;
use ActiveCollab\Retro\UI\Form\Select\Select;
use ActiveCollab\Retro\UI\Indicator\Badge;
use ActiveCollab\Retro\UI\Indicator\Icon;
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

    public function testWillRenderOptionWithAdornments(): void
    {
        $renderedSelectOption = $this->renderer->renderSelectOption(
            (new Option('Option 1', 'option_1'))->adornments(
                new Icon('star'),
                new Badge('15'),
            ),
        );

        $this->assertStringStartsWith('<sl-option', $renderedSelectOption);
        $this->assertStringContainsString('<sl-icon name="star" slot="prefix"></sl-icon>', $renderedSelectOption);
        $this->assertStringContainsString('<sl-badge variant="primary" slot="suffix">15</sl-badge>', $renderedSelectOption);
        $this->assertStringEndsWith('</sl-option>', $renderedSelectOption);
    }

    public function testWillRenderOptionGroup(): void
    {
        $renderedOptionGroup = $this->renderer->renderSelectOptionGroup(
            new OptionGroup(
                'Group 1',
                new Option('Option 1', 'option_1'),
                new Option('Option 2', 'option_2'),
            ),
        );

        $this->assertStringContainsString('<small>Group 1</small>', $renderedOptionGroup);
        $this->assertStringContainsString('<sl-option value="option_1">Option 1</sl-option>', $renderedOptionGroup);
        $this->assertStringContainsString('<sl-option value="option_2">Option 2</sl-option>', $renderedOptionGroup);
    }
}
