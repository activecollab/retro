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
use ActiveCollab\Retro\UI\Renderer\Shoelace\ShoelaceRenderer;
use ActiveCollab\Retro\UI\Surface\Details\Details;

class DetailsTest extends TestCase
{
    private ShoelaceRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renderer = new ShoelaceRenderer(
            $this->createMock(ComponentIdResolverInterface::class),
        );
    }

    public function testWillRenderDetails(): void
    {
        $renderedDetails = $this->renderer->renderDetails(
            new Details(
                'Toggle Details',
                'Here are the details',
            ),
        );

        $this->assertStringStartsWith('<sl-details', $renderedDetails);
        $this->assertStringNotContainsString('open', $renderedDetails);
        $this->assertStringContainsString('summary="Toggle Details"', $renderedDetails);
        $this->assertStringContainsString('Here are the details', $renderedDetails);
        $this->assertStringEndsWith('</sl-details>', $renderedDetails);
    }

    public function testWillRenderIcons(): void
    {
        $renderedDetails = $this->renderer->renderDetails(
            (new Details(
                'Toggle Details',
                'Here are the details',
            ))->icon(
                new Icon('info-circle'),
                new Icon('x-lg'),
            ),
        );

        $this->assertStringContainsString('<sl-icon name="info-circle" slot="expand-icon"></sl-icon>', $renderedDetails);
        $this->assertStringContainsString('<sl-icon name="x-lg" slot="collapse-icon"></sl-icon>', $renderedDetails);
    }
}
