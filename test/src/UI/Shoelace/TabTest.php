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
use ActiveCollab\Retro\UI\Navigation\Tab\Tab;
use ActiveCollab\Retro\UI\Navigation\Tab\TabGroup;
use ActiveCollab\Retro\UI\Renderer\Shoelace\ShoelaceRenderer;

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
    }
}
