<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Service\ExecutionRecorder\Factory;

use ActiveCollab\Retro\Service\Context\ServiceContextInterface;
use ActiveCollab\Retro\Service\ExecutionRecorder\ServiceExecutionRecorderInterface;

interface ServiceExecutionRecorderFactoryInterface
{
    public function create(): ServiceExecutionRecorderInterface;
    public function createWithinContext(ServiceContextInterface $context): ServiceExecutionRecorderInterface;
}
