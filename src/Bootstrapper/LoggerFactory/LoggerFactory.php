<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\LoggerFactory;

use ActiveCollab\Retro\Bootstrapper\Metadata\NameInterface;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;

class LoggerFactory implements LoggerFactoryInterface
{
    public function __construct(
        private NameInterface $appName,
    )
    {
    }

    public function createLogger(HandlerInterface ...$handlers): LoggerInterface
    {
        return $this->createLoggerWithFormatter(
            new LineFormatter(
                "[%datetime%] %level_name%: %message% %context% %extra%\n",
                'Y-m-d H:i:s',
            ),
            ...$handlers,
        );
    }

    public function createLoggerWithFormatter(
        FormatterInterface $formatter,
        HandlerInterface ...$handlers,
    ): LoggerInterface
    {
        $logger = new Logger($this->appName->getName());
        $processor = new PsrLogMessageProcessor();

        foreach ($handlers as $handler) {
            $handler->setFormatter($formatter);
            $handler->pushProcessor($processor);

            $logger->pushHandler($handler);
        }

        return $logger;
    }
}
