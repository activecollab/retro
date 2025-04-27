<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Rpc\Result\Json;

use ActiveCollab\Retro\Rpc\Result\FailureInterface;
use ActiveCollab\Retro\Rpc\Result\ResultInterface;
use ActiveCollab\Retro\Rpc\Result\SuccessInterface;

class JsonRpcResult implements JsonRpcResultInterface
{
    public function __construct(
        private ResultInterface $serviceCallResult,
        private string|int|float|null $serviceCallId = null,
    )
    {
    }

    public function isSuccess(): bool
    {
        return $this->serviceCallResult->isSuccess();
    }

    public function getResult(): mixed
    {
        if ($this->serviceCallResult instanceof SuccessInterface) {
            return $this->serviceCallResult->getResult();
        }

        return null;
    }

    public function getServiceCallId(): float|int|string|null
    {
        return $this->serviceCallId;
    }

    public function jsonSerialize(): array
    {
        $result = [
            'jsonrpc' => '2.0',
        ];

        if ($this->serviceCallResult instanceof SuccessInterface) {
            $result['result'] = $this->serviceCallResult->getResult();
        } elseif ($this->serviceCallResult instanceof FailureInterface) {
            $result['error'] = $this->serviceCallResult->getFailureReason()->getMessage();
        }

        if ($this->serviceCallId !== null) {
            $result['id'] = $this->serviceCallId;
        }

        return $result;
    }
}
