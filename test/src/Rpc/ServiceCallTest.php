<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\Rpc;

use ActiveCollab\Retro\Rpc\RpcServer;
use ActiveCollab\Retro\Test\Base\TestCase;
use ActiveCollab\Retro\Test\Fixtures\TestBundle\Service\DoStuffService;
use ActiveCollab\Retro\Test\Fixtures\TestBundle\TestBundle;
use ActiveCollab\Sitemap\Nodes\Node;
use LogicException;

class ServiceCallTest extends TestCase
{
    public function testWillRequireServiceClasses(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Please provide service classes.');

        (new RpcServer())->registerService(TestBundle::class);
    }

    public function testWillRejectNonBundles(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Bundle class must implement BundleInterface.');

        (new RpcServer())->registerService(Node::class, DoStuffService::class);
    }

    public function testWillRejectNonServices(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Service class must implement ServiceInterface.');

        (new RpcServer())->registerService(TestBundle::class, Node::class);
    }

    public function testWillRegisterServiceMethods(): void
    {
        $server = new RpcServer();
        $server->registerService(TestBundle::class, DoStuffService::class);

        $this->assertTrue($server->hasMethod(TestBundle::class, DoStuffService::class, 'sumTwoNumbers'));
        $this->assertTrue($server->hasMethod(TestBundle::class, DoStuffService::class, 'recordClick'));
        $this->assertFalse($server->hasMethod(TestBundle::class, DoStuffService::class, 'notAnRpcMethod'));
    }

    public function testWillCallService(): void
    {
        $server = new RpcServer();
        $server->registerService(TestBundle::class, DoStuffService::class);

        $result = $server->json('
            {
                "jsonrpc": "2.0",
                "method": "Test.DoStuff.sumTwoNumbers",
                "params": [1, 2],
                "id": 3
            }
        ');

        $this->assertInstanceof(Success::class, $result);
        $this->assertSame( 'call-signature', $result->getMessage());
        $this->assertSame( 3, $result->getCallId());
    }
}