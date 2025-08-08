<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\LoggerFactory;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Psr\Log\LoggerInterface;

interface LoggerFactoryInterface
{
    public function createLogger(HandlerInterface ...$handlers): LoggerInterface;
    public function createLoggerWithFormatter(
        FormatterInterface $formatter,
        HandlerInterface ...$handlers,
    ): LoggerInterface;
}
