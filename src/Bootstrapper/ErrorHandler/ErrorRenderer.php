<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Bootstrapper\ErrorHandler;

use ActiveCollab\TemplateEngine\TemplateEngineInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Interfaces\ErrorRendererInterface;
use Throwable;

class ErrorRenderer implements ErrorRendererInterface
{
    private TemplateEngineInterface $templateEngine;

    public function __construct(TemplateEngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        $httpErrorCode = $this->getHttpErrorCodeFromException($exception);

        return $this->templateEngine->fetch(
            $this->templateEngine->getTemplatePath(sprintf('__errors/%d.tpl', $httpErrorCode)),
            [
                'exception' => $exception,
                'exceptionType' => get_class($exception),
                'displayErrorDetails' => $displayErrorDetails,
            ]
        );
    }

    private function getHttpErrorCodeFromException(Throwable $exception): int
    {
        if ($exception instanceof HttpBadRequestException) {
            return 400;
        } elseif ($exception instanceof HttpUnauthorizedException) {
            return 401;
        } elseif ($exception instanceof HttpForbiddenException) {
            return 403;
        } elseif ($exception instanceof HttpNotFoundException) {
            return 404;
        } elseif ($exception instanceof HttpMethodNotAllowedException) {
            return 405;
        }

        return 500;
    }
}
