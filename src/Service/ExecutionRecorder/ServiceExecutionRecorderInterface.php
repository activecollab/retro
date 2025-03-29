<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Service\ExecutionRecorder;

use ActiveCollab\Retro\Service\Result\ServiceResultInterface;
use ActiveCollab\User\UserInterface;

interface ServiceExecutionRecorderInterface
{
    public function record(
        string $executionEvent,
        ?UserInterface $user,
        ?array $metadata = null,
    ): ServiceResultInterface;
}
