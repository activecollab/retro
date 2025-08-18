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
        $this->assertStringContainsString('open', $renderedDetails);
        $this->assertStringContainsString('summary="Toggle Details"', $renderedDetails);
        $this->assertStringContainsString('Here are the details', $renderedDetails);
        $this->assertStringEndsWith('</sl-details>', $renderedDetails);
    }
}
