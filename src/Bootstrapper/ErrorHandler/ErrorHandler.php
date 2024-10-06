<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\ErrorHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Handlers\ErrorHandler as BaseErrorHandler;
use Slim\Interfaces\CallableResolverInterface;
use Throwable;

class ErrorHandler extends BaseErrorHandler
{
    private int $exception_encoding_level = 1;

    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        LoggerInterface $logger,
    ) {
        parent::__construct($callableResolver, $responseFactory, $logger);
    }

    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        $this->logger->error(
            'Error while handling {method} request for {uri}.',
            array_merge(
                [
                    'method' => $request->getMethod(),
                    'uri' => (string) $request->getUri(),
                    'body' => $request->getParsedBody(),
                ],
                $this->serializeException($exception)
            )
        );

        return parent::__invoke($request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails);
    }

    private function serializeException(
        Throwable $exception,
        string $argument_name = 'exception',
        array $context = null
    ): array {
        if (empty($context)) {
            $context = [];
        }

        $context[$argument_name] = $exception->getMessage();

        $context["{$argument_name}_class"] = get_class($exception);
        $context["{$argument_name}_code"] = $exception->getCode();
        $context["{$argument_name}_file"] = $exception->getFile();
        $context["{$argument_name}_line"] = $exception->getLine();
        $context["{$argument_name}_trace"] = $exception->getTraceAsString();
        $context["{$argument_name}_class"] = get_class($exception);

        if ($exception->getPrevious() && $this->exception_encoding_level <= 3) {
            ++$this->exception_encoding_level;

            $context = $this->serializeException(
                $exception->getPrevious(),
                "{$argument_name}_previous",
                $context
            );
        }

        return $context;
    }

    protected function logError(string $error): void
    {
        $this->logger->error($error);
    }
}
