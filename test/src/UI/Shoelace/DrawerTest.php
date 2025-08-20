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
use ActiveCollab\Retro\UI\Surface\Drawer\Drawer;

class DrawerTest extends TestCase
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
        $renderedDrawer = $this->renderer->renderDrawer(
            new Drawer(
                'See the contents',
                'Socks and stuff.',
            ),
        );

        $this->assertStringStartsWith('<sl-drawer', $renderedDrawer);
        $this->assertStringContainsString('label="See the contents"', $renderedDrawer);
        $this->assertStringContainsString('Socks and stuff.', $renderedDrawer);
        $this->assertStringEndsWith('</sl-drawer>', $renderedDrawer);
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
