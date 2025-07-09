<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro\Queue;

use ActiveCollab\JobsQueue\JobsDispatcherInterface;
use ActiveCollab\Retro\Command\Retro\RetroCommand;
use RuntimeException;

abstract class QueueCommand extends RetroCommand
{
    public function getCommandNamePrefix(): string
    {
        return parent::getCommandNamePrefix() . 'queue:';
    }

    protected function getJobsDispatcher(): JobsDispatcherInterface
    {
        $jobsDispatcher = $this->get(JobsDispatcherInterface::class);

        if (!$jobsDispatcher instanceof JobsDispatcherInterface) {
            throw new RuntimeException('Failed to get jobs dispatcher from DI container');
        }

        return $jobsDispatcher;
    }
}
