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
use ActiveCollab\Retro\UI\Navigation\Tab\Tab;
use ActiveCollab\Retro\UI\Navigation\Tab\TabGroup;
use ActiveCollab\Retro\UI\Renderer\Shoelace\ShoelaceRenderer;
use LogicException;

class TabTest extends TestCase
{
    private ComponentIdResolverInterface $componentIdResolver;
    private ShoelaceRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->componentIdResolver = $this->createMock(ComponentIdResolverInterface::class);
        $this->renderer = new ShoelaceRenderer($this->componentIdResolver);
    }

    public function testWillRenderPreRenderedElement(): void
    {
        $renderedTabGroup = $this->renderer->renderTabGroup(
            new TabGroup(
                new PreRenderedElement('Hello!'),
            ),
        );

        $this->assertStringContainsString('Hello!', $renderedTabGroup)  ;
    }

    public function testWillNotAllowPreRenderedContentAndAdditionalTabs(): void
    {
        $this->expectException(LogicException::class);

        $this->renderer->renderTabGroup(
            new TabGroup(
                new PreRenderedElement('Hello!'),
                new Tab('Tab 1', 'Content for tab 1'),
            ),
        );
    }

    public function testWillConnectTabAndPanel(): void
    {
        $this->componentIdResolver
            ->expects($this->exactly(2))
            ->method('getUniqueId')
            ->with('tab')
            ->willReturnOnConsecutiveCalls('tab-1', 'tab-2');

        $renderedTabGroup = $this->renderer->renderTabGroup(
            new TabGroup(
                new Tab('Entities', 'List of entities'),
                new Tab('Settings', 'Configuration options'),
            ),
        );

        $this->assertStringContainsString('<sl-tab-group>', $renderedTabGroup);
        $this->assertStringContainsString('panel="tab-1"', $renderedTabGroup);
        $this->assertStringContainsString('panel="tab-2"', $renderedTabGroup);
        $this->assertStringContainsString('name="tab-1"', $renderedTabGroup);
        $this->assertStringContainsString('name="tab-2"', $renderedTabGroup);
        $this->assertStringContainsString('slot="nav"', $renderedTabGroup);
    }
}
