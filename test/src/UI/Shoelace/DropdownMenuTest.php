<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\UI\Shoelace;

use ActiveCollab\Retro\Test\Base\TestCase;
use ActiveCollab\Retro\UI\Indicator\Badge;
use ActiveCollab\Retro\UI\Button\Button;
use ActiveCollab\Retro\UI\Dropdown\Dropdown;
use ActiveCollab\Retro\UI\Dropdown\Menu\Element\MenuItem\MenuItem;
use ActiveCollab\Retro\UI\Dropdown\Menu\Menu;
use ActiveCollab\Retro\UI\Indicator\Icon;
use ActiveCollab\Retro\UI\Renderer\Shoelace\ShoelaceRenderer;

class DropdownMenuTest extends TestCase
{
    public function testDropdownTriggerWillHaveSlot(): void
    {
        $renderedDropdown = (new ShoelaceRenderer())->renderDropdown(
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

    public function testMenuUtimWillRenderAdornments(): void
    {
        $renderedMenuItem = (new ShoelaceRenderer())->renderMenuItem(
            new MenuItem(
                'Hello World!',
                leftAdornment: new Icon('alphabet'),
                rightAdornment: new Badge('123'),
            ),
        );

        $this->assertStringContainsString('Hello World!', $renderedMenuItem);
        $this->assertStringContainsString('slot="prefix"', $renderedMenuItem);
        $this->assertStringContainsString('slot="suffix"', $renderedMenuItem);
    }
}
