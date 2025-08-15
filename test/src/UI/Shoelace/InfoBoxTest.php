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
use ActiveCollab\Retro\UI\Indicator\Icon;
use ActiveCollab\Retro\UI\Indicator\InfoBox;
use ActiveCollab\Retro\UI\Renderer\Shoelace\ShoelaceRenderer;

class InfoBoxTest extends TestCase
{
    private ShoelaceRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renderer = new ShoelaceRenderer(
            $this->createMock(ComponentIdResolverInterface::class),
        );
    }

    public function testWillRender(): void
    {
        $renderedInfoBox = $this->renderer->renderInfoBox(
            new InfoBox('Important information goes here.'),
        );

        $this->assertStringStartsWith('<sl-alert', $renderedInfoBox);
        $this->assertStringContainsString('Important information goes here.', $renderedInfoBox);
        $this->assertStringEndsWith('</sl-alert>', $renderedInfoBox);
    }

    public function testWillRenderIcon(): void
    {
        $renderedInfoBox = $this->renderer->renderInfoBox(
            new InfoBox(
                'Important information goes here.',
                icon: new Icon('info-in-circle'),
            ),
        );

        $this->assertStringContainsString('<sl-icon', $renderedInfoBox);
        $this->assertStringContainsString('slot="icon"', $renderedInfoBox);
        $this->assertStringContainsString('</sl-icon>', $renderedInfoBox);
    }
}
