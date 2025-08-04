<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Service;

use ActiveCollab\DatabaseConnection\ConnectionInterface;
use ActiveCollab\DatabaseObject\Exception\ValidationException;
use ActiveCollab\DatabaseObject\PoolInterface;
use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\Service\Result\Factory\ServiceResultFactoryInterface;
use Exception;
use ActiveCollab\Retro\Service\Result\InvalidFormData\InvalidFormData;
use ActiveCollab\Retro\Service\Result\InvalidFormData\InvalidFormDataInterface;
use ActiveCollab\Retro\Service\Result\RequestProcessingFailed\RequestProcessingFailed;
use ActiveCollab\Retro\Service\Result\RequestProcessingFailed\RequestProcessingFailedInterface;
use ActiveCollab\Retro\Service\Result\ServiceResultInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

abstract class Service implements ServiceInterface
{
    public function __construct(
        protected ConnectionInterface $connection,
        protected PoolInterface $pool,
        protected LoggerInterface $logger,
        protected ServiceResultFactoryInterface $serviceResultFactory,
    )
    {
    }

    private array $afterTransaction = [];

    /**
     * Execute after transaction ran by withinTransaction() method. Use this when you need to keep something in the
     * database even in cases of non-success service result (like error logs, rate limitation logs etc.)
     */
    protected function afterTransaction(callable $execute): void
    {
        $this->afterTransaction[] = $execute;
    }

    private array $onTransactionException = [];

    /**
     * Execute in case of transaction exception. Exception will roll back the transaction. Use these callbacks in case
     * you need to keep something in the database, like error log.
     */
    protected function onTransactionException(callable $execute): void
    {
        $this->onTransactionException[] = $execute;
    }

    protected function withinTransaction(
        callable $callback,
        ?FormDataInterface $formData,
    ): ServiceResultInterface
    {
        try {
            $this->connection->beginWork();

            $result = call_user_func($callback);

            if (!$result instanceof ServiceResultInterface) {
                throw new RuntimeException('Invalid result returned from callback.');
            }

            if ($result->isSuccess()) {
                $this->connection->commit();
            } else {
                $this->connection->rollback();
            }

            if (!empty($this->afterTransaction)) {
                $this->connection->transact(
                    function () {
                        foreach ($this->afterTransaction as $execute) {
                            call_user_func($execute);
                        }
                    },
                );
            }

            return $result;
        } catch (Exception $e) {
            $this->connection->rollback();

            if (!empty($this->onTransactionException)) {
                $this->connection->transact(
                    function () use ($e) {
                        foreach ($this->onTransactionException as $execute) {
                            call_user_func($execute, $e);
                        }
                    },
                );
            }

            return $this->processingExceptionToResult($e, $formData);
        }
    }

    protected function processingExceptionToResult(
        Exception $exception,
        FormDataInterface $formData = null,
    ): InvalidFormDataInterface|RequestProcessingFailedInterface
    {
        if ($formData) {
            if ($exception instanceof ValidationException) {
                $this->logger->warning(
                    'Validation of {object} object failed.',
                    [
                        'service' => $this::class,
                        'object' => $exception->getObject() ? $exception->getObject()::class : '-- unknown --',
                        'errors' => $exception->getErrors(),
                    ],
                );

                return new InvalidFormData($formData->addFromValidationException($exception));
            }

            $formData->addFieldError('', $exception->getMessage());
        }

        $this->logger->error(
            'Service processing error: {exception_message}.',
            [
                'service' => $this::class,
                'exception_type' => $exception::class,
                'exception_message' => $exception->getMessage(),
                'exception_file' => $exception->getFile(),
                'exception_line' => $exception->getLine(),
            ],
        );

        return new RequestProcessingFailed($exception, $formData);
    }
}
