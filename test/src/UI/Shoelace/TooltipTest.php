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
use ActiveCollab\Retro\UI\Button\Button;
use ActiveCollab\Retro\UI\Indicator\Tooltip\Tooltip;
use ActiveCollab\Retro\UI\Renderer\Shoelace\ShoelaceRenderer;

class TooltipTest extends TestCase
{
    private ShoelaceRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renderer = new ShoelaceRenderer(
            $this->createMock(ComponentIdResolverInterface::class),
        );
    }

    public function testWillIgnoreElementThatAlreadyHasTooltip(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Element already has a tooltip.');

        $this->renderer->renderTooltip(
            new Tooltip(
                'This is a tooltip message',
                (new Button('Click to Open'))->tooltip(
                    new Tooltip('This is a tooltip message'),
                ),
            ),
        );
    }

    public function testWillRender(): void
    {
        $renderedTooltip = $this->renderer->renderTooltip(
            new Tooltip(
                'This is a tooltip message',
                new Button('Click to Open'),
            ),
        );

        $this->assertStringStartsWith('<sl-tooltip', $renderedTooltip);
        $this->assertStringContainsString('Click to Open', $renderedTooltip);
        $this->assertStringContainsString('This is a tooltip message', $renderedTooltip);
        $this->assertStringEndsWith('</sl-tooltip>', $renderedTooltip);
    }

    public function testWillRenderButtonWithTooltipAttribute(): void
    {
        $renderedButton = $this->renderer->renderButton(
            (new Button('Click to Open'))->tooltip(
                new Tooltip('This is a tooltip message'),
            ),
        );

        $this->assertStringStartsWith('<sl-tooltip', $renderedButton);
        $this->assertStringContainsString('Click to Open', $renderedButton);
        $this->assertStringContainsString('This is a tooltip message', $renderedButton);
        $this->assertStringEndsWith('</sl-tooltip>', $renderedButton);
    }
}
