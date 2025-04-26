<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\Rpc;

use ActiveCollab\Retro\Rpc\RpcServer;
use ActiveCollab\Retro\Rpc\ServiceResolverInterface;
use ActiveCollab\Retro\Test\Base\TestCase;
use ActiveCollab\Retro\Test\Fixtures\TestBundle\Service\DoStuffService;
use ActiveCollab\Retro\Test\Fixtures\TestBundle\TestBundle;
use ActiveCollab\Sitemap\Nodes\Node;
use LogicException;
use RuntimeException;

class ServiceCallTest extends TestCase
{
    public function testWillRequireServiceClasses(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Please provide service classes.');

        (new RpcServer($this->createMock(ServiceResolverInterface::class)))
            ->registerService(TestBundle::class);
    }

    public function testWillRejectNonBundles(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Bundle class must implement BundleInterface.');

        (new RpcServer($this->createMock(ServiceResolverInterface::class)))
            ->registerService(Node::class, DoStuffService::class);
    }

    public function testWillRejectNonServices(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Service class must implement ServiceInterface.');

        (new RpcServer($this->createMock(ServiceResolverInterface::class)))
            ->registerService(TestBundle::class, Node::class);
    }

    public function testWillRegisterServiceMethods(): void
    {
        $server = new RpcServer($this->createMock(ServiceResolverInterface::class));
        $server->registerService(TestBundle::class, DoStuffService::class);

        $this->assertTrue($server->hasMethod(TestBundle::class, DoStuffService::class, 'sumTwoNumbers'));
        $this->assertTrue($server->hasMethod(TestBundle::class, DoStuffService::class, 'recordClick'));
        $this->assertFalse($server->hasMethod(TestBundle::class, DoStuffService::class, 'notAnRpcMethod'));
    }

    /**
     * @dataProvider provideInvalidJsonRpc
     */
    public function testWillRejectInvalidJsonRpc(
        string $invalidJson,
        string $expectedExceptionMessage,
    ): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expectedExceptionMessage, 'Invalid JSON-RPC request.');

        $server = new RpcServer($this->createMock(ServiceResolverInterface::class), '.');
        $server->registerService(TestBundle::class, DoStuffService::class);

        $server->json($invalidJson);
    }

    public function provideInvalidJsonRpc(): array
    {
        return [
            ['', 'Payload cannot be empty.'],
            ['not-valid-json', 'Invalid JSON payload.'],
            ['{}', 'Invalid JSON-RPC request.'],
            ['{"jsonrpc": "xyz"}', 'Invalid JSON-RPC request.'],
            ['{"jsonrpc": "2.0"}', 'Invalid JSON-RPC request.'],
            ['{"jsonrpc": "2.0", "method":""}', 'Invalid JSON-RPC request.'],
            ['{"jsonrpc": "2.0", "method":123}', 'Invalid JSON-RPC request.'],
            ['{"jsonrpc": "2.0", "method":"Test.DoStuff.sumTwoNumbers", "params":123}', 'Invalid JSON-RPC request.'],
            ['{"jsonrpc": "2.0", "method":"noseparator"}', 'Invalid JSON-RPC method name.'],
            ['{"jsonrpc": "2.0", "method":"missing.element"}', 'Invalid JSON-RPC method name.'],
            ['{"jsonrpc": "2.0", "method":"123.456.789"}', 'Invalid JSON-RPC method name.'],
            ['{"jsonrpc": "2.0", "method":"Unknown.DoStuff.sumTwoNumbers"}', 'Unknown bundle.'],
            ['{"jsonrpc": "2.0", "method":"Test.Unknown.sumTwoNumbers"}', 'Unknown service.'],
            ['{"jsonrpc": "2.0", "method":"Test.DoStuff.unknown"}', 'Unknown method.'],
        ];
    }

    public function testWillCallService(): void
    {
        $server = new RpcServer($this->createMock(ServiceResolverInterface::class), '.');
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