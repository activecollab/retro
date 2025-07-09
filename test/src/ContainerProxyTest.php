<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test;

use ActiveCollab\Retro\Bootstrapper\ContainerProxy\ContainerProxy;
use ActiveCollab\Retro\Service\ServiceInterface;
use ActiveCollab\Retro\Test\Base\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

class ContainerProxyTest extends TestCase
{
    public function testProxyIsContainer(): void
    {
        $this->assertInstanceOf(
            ContainerInterface::class,
            new ContainerProxy(
                $this->createMock(ContainerInterface::class),
                [],
            ),
        );
    }

    public function testNotAccessContainerWithoutAlias(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->never())
            ->method('has');

        $containerProxy = new ContainerProxy(
            $container,
            [],
        );

        $containerProxy->has('service');
    }

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

    public function testWillAccessServiceViaAlias(): void
    {
        $service = new stdClass();

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('has')
            ->with('service')
            ->willReturn(true);
        $container
            ->expects($this->once())
            ->method('get')
            ->with('service')
            ->willReturn($service);

        $containerProxy = new ContainerProxy(
            $container,
            [
                'alias' => 'service',
            ]
        );

        $containerProxy->get('alias');
    }
}
