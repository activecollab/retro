<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Rpc\Result\Json;

use ActiveCollab\Retro\Rpc\Result\ResultInterface;
use JsonSerializable;

interface JsonResultInterface extends ResultInterface, JsonSerializable
{
    public function isSuccess(): bool;
    public function getResult(): mixed;
    public function getServiceCallId(): float|int|string|null;
    public function jsonSerialize(): array;
}
