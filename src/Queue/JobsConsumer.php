<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Queue;

use ActiveCollab\JobsQueue\Jobs\Job;
use ActiveCollab\JobsQueue\JobsDispatcherInterface;
use ActiveCollab\Retro\Queue\Timer\JobsConsumerTimerInterface;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class JobsConsumer implements JobsConsumerInterface
{
    public function __construct(
        private JobsDispatcherInterface $jobsDispatcher,
        private LoggerInterface $logger,
        private ContainerInterface $container,
        private JobsConsumerTimerInterface $timer,
    )
    {
    }

    public function run(callable $writeLine): int
    {
        $jobsRan = [];
        $jobsFailed = [];

        $this->jobsDispatcher->getQueue()->onJobFailure(
            function (Job $job, Exception $e) use (&$jobsFailed, $writeLine) {
                $jobId = $job->getQueueId();

                if (!in_array($jobId, $jobsFailed)) {
                    $jobsFailed[] = $jobId;
                }

                $this->writeLn(
                    $writeLine,
                    sprintf(
                        'Exception %s: %s',
                        $e::class,
                        $e->getMessage(),
                    ),
                    sprintf(
                        'Exception throw at %s line %d',
                        $e->getFile(),
                        $e->getLine(),
                    ),
                    $e->getTraceAsString(),
                );
            },
        );

        $referenceTime = microtime(true);

        $jobsCount = $this->jobsDispatcher->getQueue()->count();

        if (empty($jobsCount)) {
            $this->writeLn($writeLine, 'Queue is empty.');

            return $this->done($writeLine);
        }

        $this->writeLn(
            $writeLine,
            sprintf(
                'There are %d jobs in the queue.',
                $jobsCount,
            ),
        );

        // ---------------------------------------------------
        //  Set max execution time for the jobs in queue
        // ---------------------------------------------------

        $this->writeLn(
            $writeLine,
            sprintf(
                'Preparing to work for %d seconds.',
                $this->timer->getRuntime(),
            ),
            '',
        );

        $workUntil = $this->timer->getWorkUntil(); // Assume that we spent 1 second bootstrapping the command

        // ---------------------------------------------------
        //  Enter the execution loop
        // ---------------------------------------------------

        do {
            $nextInLine = $this->jobsDispatcher->getQueue()->nextInLine();

            if (empty($nextInLine)) {
                break;
            }

            $this->logger->debug(
                'Running job #{job_id} of {job_type} type',
                [
                    'job_type' => $nextInLine::class,
                    'job_id' => $nextInLine->getQueueId(),
                ],
            );

            $this->writeLn(
                $writeLine,
                sprintf(
                    'Running job #%s (%s).',
                    $nextInLine->getQueueId(),
                    $nextInLine::class,
                ),
            );

            if (method_exists($nextInLine, 'setContainer')) {
                $nextInLine->setContainer($this->container);
            }

            $this->jobsDispatcher->getQueue()->execute($nextInLine);

            $this->writeLn(
                $writeLine,
                sprintf(
                    'Job #%d done.',
                    $nextInLine->getQueueId(),
                ),
            );

            $jobId = $nextInLine->getQueueId();

            if (!in_array($jobId, $jobsRan)) {
                $jobsRan[] = $jobId;
            }
        } while (time() < $workUntil);

        // ---------------------------------------------------
        //  Print stats
        // ---------------------------------------------------

        $leftInQueue = $this->jobsDispatcher->getQueue()->count();

        $this->logger->debug(
            '{jobs_ran} jobs ran in {exec_time}s',
            [
                'time_limit' => $this->timer->getRuntime(),
                'exec_time' => round(microtime(true) - $referenceTime, 3),
                'jobs_ran' => count($jobsRan),
                'jobs_failed' => count($jobsFailed),
                'left_in_queue' => $leftInQueue,
            ],
        );

        $this->writeLn(
            $writeLine,
            sprintf(
                'Execution stats: %d ran, %d failed. %d left in queue. Executed in %s',
                count($jobsRan),
                count($jobsFailed),
                $leftInQueue,
                round(microtime(true) - $referenceTime, 3),
            ),
            '',
        );

        return $this->done($writeLine);
    }

    private function done(callable $writeLine): int
    {
        $this->writeLn(
            $writeLine,
            sprintf(
                'Done in %s seconds.',
                round(microtime(true) - $this->timer->getStartTime(), 5),
            ),
        );

        return 0;
    }

    private function writeLn(callable $writeLine, string ...$lines): void
    {
        foreach ($lines as $line) {
            call_user_func($writeLine, $line);
        }
    }
}
