<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\Rpc;

use ActiveCollab\Retro\Rpc\Result\Failure;
use ActiveCollab\Retro\Rpc\Result\Json\JsonRpcResult;
use ActiveCollab\Retro\Rpc\Result\Json\JsonRpcResultInterface;
use ActiveCollab\Retro\Rpc\Result\Success;
use ActiveCollab\Retro\Test\Base\TestCase;

class JsonRpcResultTest extends TestCase
{
    public function testWillEncodeSuccess(): void
    {
        $decoded = $this->getDecodedResult(
            new JsonRpcResult(
                new Success(123),
                'XYZ',
            ),
        );

        $this->assertSame('2.0', $decoded['jsonrpc']);
        $this->assertSame(123, $decoded['result']);
        $this->assertSame('XYZ', $decoded['id']);
        $this->assertArrayNotHasKey('error', $decoded);
    }

    public function testWillSkipCallIdWhenNotSet(): void
    {
        $decoded = $this->getDecodedResult(
            new JsonRpcResult(
                new Success(123),
            ),
        );

        $this->assertArrayNotHasKey('id', $decoded);
    }

    public function testWillEncodeFailure(): void
    {
        $decoded = $this->getDecodedResult(
            new JsonRpcResult(
                new Failure(new \RuntimeException('Something bad happened')),
                'XYZ',
            ),
        );

        $this->assertSame('2.0', $decoded['jsonrpc']);
        $this->assertSame('Something bad happened', $decoded['error']);
        $this->assertSame('XYZ', $decoded['id']);
        $this->assertArrayNotHasKey('result', $decoded);
    }

    private function getDecodedResult(JsonRpcResultInterface $result): array
    {
        return json_decode(json_encode($result), true);
    }
}
