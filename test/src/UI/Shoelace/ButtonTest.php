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
use ActiveCollab\Retro\UI\Indicator\Badge;
use ActiveCollab\Retro\UI\Indicator\Icon;
use ActiveCollab\Retro\UI\Renderer\Shoelace\ShoelaceRenderer;

class ButtonTest extends TestCase
{
    private ShoelaceRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renderer = new ShoelaceRenderer(
            $this->createMock(ComponentIdResolverInterface::class),
        );
    }

    public function testWillRenderButton(): void
    {
        $renderedButton = $this->renderer->renderButton(
            new Button('Click to Open'),
        );

        $this->assertStringStartsWith('<sl-button', $renderedButton);
        $this->assertStringEndsWith('</sl-button>', $renderedButton);
        $this->assertStringContainsString('Click to Open', $renderedButton);
    }

    public function testWillRenderIcon(): void
    {
        $renderedButton = $this->renderer->renderButton(
            new Button(new Icon('x-lg')),
        );

        $this->assertStringContainsString('<sl-icon name="x-lg"></sl-icon>', $renderedButton);
    }

    public function testButtonWillRenderAdornments(): void
    {
        $renderedMenuItem = $this->renderer->renderButton(
            new Button(
                'Click to Open',
                leftAdornment: new Icon('alphabet'),
                rightAdornment: new Badge('123'),
            ),
        );

        $this->assertStringContainsString('Click to Open', $renderedMenuItem);
        $this->assertStringContainsString('slot="prefix"', $renderedMenuItem);
        $this->assertStringContainsString('slot="suffix"', $renderedMenuItem);
    }
}
