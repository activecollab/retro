<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\FormData;

use ActiveCollab\DatabaseObject\Exception\ValidationException;
use Psr\Http\Message\ServerRequestInterface;

interface FormDataInterface
{
    public function extractStringFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
        string $default = '',
    ): string;

    public function extractTrimmedStringFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
        string $default = '',
    ): string;

    public function extractIntFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
        int $default = 0,
    ): int;

    public function extractOptionalIntFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
    ): ?int;

    public function extractFloatFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
        float $default = 0,
    ): float;

    public function extractOptionalFloatFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
    ): ?float;

    public function extractBoolFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
    ): bool;

    public function extractArrayFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
        array $default = [],
    ): array;

    public function extractArrayOfIdsFromRequest(
        ServerRequestInterface $request,
        string $fieldName,
        array $default = [],
    ): array;

    public function getFieldValue(string $fieldName, mixed $default = null): mixed;
    public function setFieldValue(string $fieldName, mixed $value): void;

    public function hasErrors(): bool;
    public function getErrors(): array;
    public function getFieldErrors(string $fieldName): ?array;
    public function hasFieldErrors(string $fieldName): bool;

    public function addFieldError(string $fieldName, string $error): void;
    public function addFromValidationException(
        ValidationException $validationException,
        string $prefix = '',
    ): self;
}
