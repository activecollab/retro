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
use ActiveCollab\Retro\UI\Common\Size;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Form\Radio\Radio;
use ActiveCollab\Retro\UI\Form\Radio\RadioGroup;
use ActiveCollab\Retro\UI\Renderer\Shoelace\ShoelaceRenderer;

class RadioGroupTest extends TestCase
{
    private ShoelaceRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renderer = new ShoelaceRenderer(
            $this->createMock(ComponentIdResolverInterface::class),
        );
    }

    public function testWilLRender(): void
    {
        $renderedRadioGroup = $this->renderer->renderRadioGroup(
            (new RadioGroup(
                'radio-group-name',
                'Radio Group Label',
                54321,
                new Radio('Option 1', 12345, true),
            ))->size(Size::LARGE)->explainer('Choose wisely'),
        );

        $this->assertStringStartsWith('<sl-radio-group', $renderedRadioGroup);
        $this->assertStringEndsWith('</sl-radio-group>', $renderedRadioGroup);
        $this->assertStringContainsString('size="large"', $renderedRadioGroup);
        $this->assertStringContainsString('name="radio-group-name"', $renderedRadioGroup);
        $this->assertStringContainsString('help-text="Choose wisely"', $renderedRadioGroup);
        $this->assertStringContainsString('Radio Group Label', $renderedRadioGroup);
        $this->assertStringContainsString('54321', $renderedRadioGroup);
    }

    public function testWillRenderRadios(): void
    {
        $renderedRadioGroup = $this->renderer->renderRadioGroup(
            new RadioGroup(
                'radio-group-name',
                'Radio Group Label',
                54321,
                new Radio('Option 1', 12345, true),
                new Radio('Option 2', 54321),
            ),
        );

        $this->assertStringContainsString('<sl-radio value="12345" disabled>', $renderedRadioGroup);
        $this->assertStringContainsString('<sl-radio value="54321">', $renderedRadioGroup);
        $this->assertStringContainsString('Option 1', $renderedRadioGroup);
        $this->assertStringContainsString('Option 2', $renderedRadioGroup);
    }

    public function testWillRenderPreRenderedElement(): void
    {
        $renderedRadioGroup = $this->renderer->renderRadioGroup(
            new RadioGroup(
                'radio-group-name',
                'Radio Group Label',
                54321,
                new PreRenderedElement('Pre-rendered content'),
            ),
        );

        $this->assertStringStartsWith('<sl-radio-group', $renderedRadioGroup);
        $this->assertStringEndsWith('</sl-radio-group>', $renderedRadioGroup);
        $this->assertStringContainsString('radio-group-name', $renderedRadioGroup);
        $this->assertStringContainsString('Radio Group Label', $renderedRadioGroup);
        $this->assertStringContainsString('54321', $renderedRadioGroup);
        $this->assertStringContainsString('Pre-rendered content', $renderedRadioGroup);
    }
}
