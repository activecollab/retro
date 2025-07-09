<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro\Queue;

use ActiveCollab\CurrentTimestamp\CurrentTimestampInterface;
use ActiveCollab\Retro\Bootstrapper\ContainerProxy\ContainerProxy;
use ActiveCollab\Retro\Integrate\Queue\ContainerAliasesResolverInterface;
use ActiveCollab\Retro\Queue\JobsConsumer;
use ActiveCollab\Retro\Queue\Timer\JobsConsumerTimer;
use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends QueueCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Run jobs from the queue.')
            ->addOption(
                'runtime',
                'r',
                InputOption::VALUE_REQUIRED,
                'The maximum runtime of the command in seconds. Default is 59 seconds.',
                59,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $runtime = $this->mustGetRuntime($input);

            $jobsConsumer = new JobsConsumer(
                $this->getJobsDispatcher(),
                $this->get(LoggerInterface::class),
                new ContainerProxy(
                    $this->getContainer(),
                    $this->get(ContainerAliasesResolverInterface::class)->getContainerAliases(),
                ),
                new JobsConsumerTimer(
                    $this->get(CurrentTimestampInterface::class),
                    microtime(true),
                    $runtime,
                ),
            );

            $jobsConsumer->run(
                fn (string $line) => $output->writeln($line),
            );

            return 0;
        } catch (Exception $e) {
            return $this->abortDueToException($e, $input, $output);
        }
    }

    private function mustGetRuntime(InputInterface $input): int
    {
        $runtime = $input->getOption('runtime');

        if (!is_numeric($runtime) || $runtime < 1) {
            throw new RuntimeException('Invalid runtime value. It must be a positive integer.');
        }

        return (int) $runtime;
    }
}
