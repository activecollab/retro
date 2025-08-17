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
use ActiveCollab\Retro\UI\Form\Radio\RadioGroup;
use ActiveCollab\Retro\UI\Renderer\Shoelace\ShoelaceRenderer;

class RadioGroupTest extends TestCase
{
    private ShoelaceRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renderer = new ShoelaceRenderer(
            $this->createMock(ComponentIdResolverInterface::class),
        );
    }

    public function testWilLRender(): void
    {
        $renderedRadioGroup = $this->renderer->renderRadioGroup(
            new RadioGroup(
                'radio-group-name',
                'Radio Group Label',
                54321,
            ),
        );

        $this->assertStringStartsWith('<sl-radio-group', $renderedRadioGroup);
        $this->assertStringEndsWith('</sl-radio-group>', $renderedRadioGroup);
        $this->assertStringContainsString('radio-group-name', $renderedRadioGroup);
        $this->assertStringContainsString('Radio Group Label', $renderedRadioGroup);
        $this->assertStringContainsString('54321', $renderedRadioGroup);
    }
}
