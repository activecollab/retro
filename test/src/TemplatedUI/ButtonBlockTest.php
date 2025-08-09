<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\TemplatedUI;

use ActiveCollab\Retro\TemplatedUI\Button\ButtonBlock;
use ActiveCollab\Retro\TemplatedUI\ComponentIdResolver\ComponentIdResolverInterface;
use ActiveCollab\Retro\TemplatedUI\Property\ButtonStyle;
use ActiveCollab\Retro\TemplatedUI\Property\Size;
use ActiveCollab\Retro\Test\Base\TestCase;
use ActiveCollab\Retro\UI\Renderer\RendererInterface;
use ActiveCollab\Retro\UI\Renderer\Shoelace\ShoelaceRenderer;

class ButtonBlockTest extends TestCase
{
    private RendererInterface $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renderer = new ShoelaceRenderer(
            $this->createMock(
                ComponentIdResolverInterface::class,
            ),
        );
    }

    public function testWillRenderButton(): void
    {
        $this->assertSame(
            '<sl-button type="button" variant="primary">Save</sl-button>',
            (new ButtonBlock($this->renderer))->render(
                'Save',
            ),
        );
    }

    public function testWillRenderSize(): void
    {
        $this->assertSame(
            '<sl-button type="button" variant="primary" size="large">Save</sl-button>',
            (new ButtonBlock($this->renderer))->render(
                'Save',
                size: Size::LARGE,
            ),
        );
    }

    /**
     * @dataProvider provideStyles
     */
    public function testWillRenderStyle(
        ButtonStyle $buttonStyle,
        string $expectedAttribute,
    )
    {
        $this->assertSame(
            sprintf(
                '<sl-button type="button" variant="primary" %s>Save</sl-button>',
                $expectedAttribute,
            ),
            (new ButtonBlock($this->renderer))->render(
                'Save',
                style: $buttonStyle,
            ),
        );
    }

    public function provideStyles(): array
    {
        return [
            [ButtonStyle::OUTLINE, ButtonStyle::OUTLINE->toAttributeName()],
            [ButtonStyle::PILL, ButtonStyle::PILL->toAttributeName()],
            [ButtonStyle::CIRCLE, ButtonStyle::CIRCLE->toAttributeName()],
            [ButtonStyle::LOADING, ButtonStyle::LOADING->toAttributeName()],
        ];
    }

    public function testWillRenderButtonWithTooltip(): void
    {
        $this->assertSame(
            '<sl-tooltip><div slot="content">Hello there!</div><sl-button type="button" variant="primary">Save</sl-button></sl-tooltip>',
            (new ButtonBlock($this->renderer))->render(
                'Save',
                tooltip: 'Hello there!'
            ),
        );
    }
}