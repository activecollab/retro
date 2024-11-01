<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command;

use ActiveCollab\ContainerAccess\ContainerAccessInterface\Implementation as ContainerAccessInterfaceImplementation;
use Doctrine\Inflector\InflectorFactory;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Throwable;

abstract class Command extends BaseCommand implements CommandInterface
{
    use ContainerAccessInterfaceImplementation;

    protected function configure()
    {
        parent::configure();

        $bits = explode('\\', get_class($this));

        $last_bit = InflectorFactory::create()->build()->tableize(array_pop($bits));
        $last_bit_len = strlen($last_bit);

        if (substr($last_bit, $last_bit_len - 8) == '_command') {
            $last_bit = substr($last_bit, 0, $last_bit_len - 8);
        }

        $this
            ->setName($this->getCommandNamePrefix() . $last_bit)
            ->addOption('debug', '', InputOption::VALUE_NONE, 'Output debug details')
            ->addOption('json', '', InputOption::VALUE_NONE, 'Output JSON');
    }

    public function getCommandNamePrefix(): string
    {
        return '';
    }

    /**
     * Abort due to error.
     */
    protected function abort(string $message, int $error_code, InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('json')) {
            $output->writeln(
                json_encode(
                    [
                        'ok' => false,
                        'error_message' => $message,
                        'error_code' => $error_code,
                    ]
                )
            );
        } else {
            $output->writeln("<error>Error #{$error_code}:</error> " . $message);
        }

        return max($error_code, 1);
    }

    /**
     * Show success message.
     */
    protected function success(string $message, InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('json')) {
            $output->writeln(json_encode([
                'ok' => true,
                'message' => $message,
            ]));
        } else {
            $output->writeln('<info>OK:</info> ' . $message);
        }

        return 0;
    }

    /**
     * Abort due to an exception.
     */
    protected function abortDueToException(Throwable $e, InputInterface $input, OutputInterface $output): int
    {
        $message = $e->getMessage();
        $code = $this->exceptionToErrorCode($e);

        if ($input->getOption('json')) {
            $response = [
                'ok' => false,
                'error_message' => $message,
                'error_code' => $code,
            ];

            if ($input->getOption('debug')) {
                $response['error_class'] = get_class($e);
                $response['error_file'] = $e->getFile();
                $response['error_line'] = $e->getLine();
                $response['error_trace'] = $e->getTraceAsString();
            }

            $output->writeln(json_encode($response));
        } else {
            $output->writeln('');

            if ($input->getOption('debug') || $output->getVerbosity()) {
                $output->writeln("<error>Error #{$code}:</error> <" . get_class($e) . '> ' . $message . ', in file ' . $e->getFile() . ' on line ' . $e->getLine());
                $output->writeln('');
                $output->writeln('Backtrace');
                $output->writeln('');
                $output->writeln($e->getTraceAsString());
            } else {
                $output->writeln("<error>Error #{$code}:</error> " . $message);
            }
        }

        return $code;
    }

    protected function askYesNoQuestion(
        string $question,
        InputInterface $input,
        OutputInterface $output
    ): bool
    {
        return in_array(
            mb_strtolower(
                $this->getHelper('question')->ask(
                    $input,
                    $output,
                    (new Question(sprintf('%s (<comment>yes</comment> or <comment>y</comment> to confirm) ', $question)))
                )
            ),
            ['y', 'yes']
        );
    }

    /**
     * Get command error code from exception.
     */
    protected function exceptionToErrorCode(Throwable $e): int
    {
        return $e->getCode() ? $e->getCode() : 1;
    }

    /**
     * @template TClassName
     * @param class-string<TClassName> $id
     * @return TClassName
     */
    public function get(string $id): mixed
    {
        return $this->getContainer()?->get($id);
    }
}
