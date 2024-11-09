<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\TemplatedUI;

use ActiveCollab\Retro\TemplatedUI\Button\ButtonBlock;
use ActiveCollab\Retro\TemplatedUI\Property\ButtonStyle;
use ActiveCollab\Retro\TemplatedUI\Property\Size;
use ActiveCollab\Retro\Test\Base\TestCase;

class ButtonTest extends TestCase
{
    public function testWillRenderButton(): void
    {
        $this->assertSame(
            '<sl-button type="button" variant="primary">Save</button>',
            (new ButtonBlock())->render(
                'Save',
            ),
        );
    }

    public function testWillRenderSize(): void
    {
        $this->assertSame(
            '<sl-button type="button" variant="primary" size="large">Save</button>',
            (new ButtonBlock())->render(
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
                '<sl-button type="button" variant="primary" %s>Save</button>',
                $expectedAttribute,
            ),
            (new ButtonBlock())->render(
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
}