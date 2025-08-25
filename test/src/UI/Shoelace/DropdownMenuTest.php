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
use ActiveCollab\Retro\UI\Action\GoToPage;
use ActiveCollab\Retro\UI\Indicator\Badge;
use ActiveCollab\Retro\UI\Button\Button;
use ActiveCollab\Retro\UI\Dropdown\Dropdown;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuItem\MenuItem;
use ActiveCollab\Retro\UI\Dropdown\Menu\Menu;
use ActiveCollab\Retro\UI\Indicator\Icon;
use ActiveCollab\Retro\UI\Renderer\Shoelace\ShoelaceRenderer;

class DropdownMenuTest extends TestCase
{
    private ShoelaceRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renderer = new ShoelaceRenderer(
            $this->createMock(ComponentIdResolverInterface::class),
        );
    }

    public function testDropdownTriggerWillHaveSlot(): void
    {
        $renderedDropdown = $this->renderer->renderDropdown(
            new Dropdown(
                new Button('Click to Open'),
                new Menu(
                    new MenuItem('Open profile'),
                ),
            ),
        );

        $this->assertStringContainsString('Click to Open', $renderedDropdown);
        $this->assertStringContainsString('<sl-button', $renderedDropdown);
        $this->assertStringContainsString('slot="trigger"', $renderedDropdown);
        $this->assertStringContainsString('<sl-menu', $renderedDropdown);
    }

    public function testMenuItemWillGoToPage(): void
    {
        $renderedDropdown = $this->renderer->renderDropdown(
            new Dropdown(
                new Button('Click to Open'),
                new Menu(
                    new MenuItem(
                        'Open profile',
                        new GoToPage('https://example.com/profile'),
                    ),
                ),
            ),
        );

        $this->assertStringContainsString('hx-get="https://example.com/profile"', $renderedDropdown);
    }

    public function testMenuItemWillRenderAdornments(): void
    {
        $renderedMenuItem = $this->renderer->renderMenuElement(
            (new MenuItem('Hello World!'))->adornments(
                new Icon('alphabet'),
                new Badge('123'),
            ),
        );

        $this->assertStringContainsString('Hello World!', $renderedMenuItem);
        $this->assertStringContainsString('slot="prefix"', $renderedMenuItem);
        $this->assertStringContainsString('slot="suffix"', $renderedMenuItem);
    }
}
