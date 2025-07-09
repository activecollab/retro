<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test;

use ActiveCollab\Retro\Bootstrapper\ContainerProxy\ContainerProxy;
use ActiveCollab\Retro\Test\Base\TestCase;
use Psr\Container\ContainerInterface;

class ContainerProxyTest extends TestCase
{
    public function testWillUseAliasAsForNumericKeys(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('has')
            ->with('service')
            ->willReturn(true);

        $containerProxy = new ContainerProxy(
            $container,
            [
                'service',
            ]
        );

        $this->assertTrue($containerProxy->has('service'));
    }
}
