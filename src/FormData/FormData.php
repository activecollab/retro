<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\FormData;

use ActiveCollab\DatabaseObject\Exception\ValidationException;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class FormData implements FormDataInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private array $fieldValues = [],
        private array $fieldErrors = [],
    )
    {
    }

    public function extractStringFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
        string $default = '',
        callable $modifier = null,
    ): string
    {
        $parsedBody = $request->getParsedBody();

        if (is_array($parsedBody) && !empty($parsedBody[$fieldName])) {
            $this->fieldValues[$fieldName] = (string) $parsedBody[$fieldName];
        } else {
            $this->fieldValues[$fieldName] = $default;
        }

        if ($modifier) {
            $this->fieldValues[$fieldName] = call_user_func($modifier, $this->fieldValues[$fieldName]);
        }

        return $this->fieldValues[$fieldName];
    }

    public function extractTrimmedStringFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
        string $default = '',
    ): string
    {
        return $this->extractStringFromRequest(
            $request,
            $fieldName,
            $default,
            fn (string $value) => trim($value),
        );
    }

    public function extractIntFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
        int $default = 0,
    ): int
    {
        $parsedBody = $request->getParsedBody();

        if (is_array($parsedBody) && array_key_exists($fieldName, $parsedBody)) {
            $this->fieldValues[$fieldName] = (int) $parsedBody[$fieldName];
        } else {
            $this->fieldValues[$fieldName] = $default;
        }

        return $this->fieldValues[$fieldName];
    }

    public function extractOptionalIntFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
    ): ?int
    {
        $parsedBody = $request->getParsedBody();

        if ($this->hasNumericField($parsedBody, $fieldName)) {
            $this->fieldValues[$fieldName] = (int) $parsedBody[$fieldName];
        } else {
            $this->fieldValues[$fieldName] = null;
        }

        return $this->fieldValues[$fieldName];
    }

    public function extractFloatFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
        float $default = 0,
    ): float
    {
        $parsedBody = $request->getParsedBody();

        if (is_array($parsedBody) && array_key_exists($fieldName, $parsedBody)) {
            $this->fieldValues[$fieldName] = (float) $parsedBody[$fieldName];
        } else {
            $this->fieldValues[$fieldName] = $default;
        }

        return $this->fieldValues[$fieldName];
    }

    public function extractOptionalFloatFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
    ): ?float
    {
        $parsedBody = $request->getParsedBody();

        if ($this->hasNumericField($parsedBody, $fieldName)) {
            $this->fieldValues[$fieldName] = (float) $parsedBody[$fieldName];
        } else {
            $this->fieldValues[$fieldName] = null;
        }

        return $this->fieldValues[$fieldName];
    }

    public function extractBoolFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
    ): bool
    {
        return !empty($request->getParsedBody()[$fieldName]);
    }

    public function extractArrayFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
        array $default = [],
    ): array
    {
        $parsedBody = $request->getParsedBody();

        if (is_array($parsedBody) && array_key_exists($fieldName, $parsedBody) && is_array($parsedBody[$fieldName])) {
            $this->fieldValues[$fieldName] = $parsedBody[$fieldName];
        } else {
            $this->fieldValues[$fieldName] = $default;
        }

        return $this->fieldValues[$fieldName];
    }

    public function extractArrayOfIdsFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
        array $default = [],
    ): array
    {
        return array_map(
            fn (mixed $value) => (int) $value,
            $this->extractArrayFromRequest($request, $fieldName, $default),
        );
    }

    private function hasNumericField(mixed $parsedBody, string $fieldName): bool
    {
        return is_array($parsedBody)
            && array_key_exists($fieldName, $parsedBody)
            && is_numeric($parsedBody[$fieldName]);
    }

    public function getFieldValue(string $fieldName, mixed $default = null): mixed
    {
        return $this->fieldValues[$fieldName] ?? $default;
    }

    public function setFieldValue(string $fieldName, mixed $value): void
    {
        $this->fieldValues[$fieldName] = $value;
    }

    public function getErrors(): array
    {
        return $this->fieldErrors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->fieldErrors);
    }

    public function getFieldErrors(string $fieldName): ?array
    {
        return $this->fieldErrors[$fieldName] ?? null;
    }

    public function hasFieldErrors(string $fieldName): bool
    {
        return !empty($this->fieldErrors[$fieldName]);
    }

    public function addFieldError(string $fieldName, string $error): void
    {
        if (empty($this->fieldErrors[$fieldName])) {
            $this->fieldErrors[$fieldName] = [];
        }

        $this->fieldErrors[$fieldName][] = $error;
    }

    public function addFromValidationException(
        ValidationException $validationException,
        string $prefix = '',
    ): self
    {
        foreach ($validationException->getErrors() as $fieldName => $fieldErrors) {
            foreach ($fieldErrors as $fieldError) {
                $this->addFieldError(
                    $this->getPrefixedFieldName($prefix, $fieldName),
                    $fieldError,
                );
            }
        }

        return $this;
    }

    private function getPrefixedFieldName(string $prefix, string $fieldName): string
    {
        if ($prefix) {
            return sprintf('%s%s', $prefix, $fieldName);
        }

        return $fieldName;
    }
}
