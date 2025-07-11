<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Rpc;

use ActiveCollab\Retro\Bootstrapper\Bundle\BundleInterface;
use ActiveCollab\Retro\Rpc\Attribute\RpcMethod;
use ActiveCollab\Retro\Rpc\Result\Failure;
use ActiveCollab\Retro\Rpc\Result\Json\JsonRpcResult;
use ActiveCollab\Retro\Rpc\Result\Json\JsonRpcResultInterface;
use ActiveCollab\Retro\Rpc\Result\ResultInterface;
use ActiveCollab\Retro\Rpc\Result\Success;
use ActiveCollab\Retro\Service\ServiceInterface;
use ActiveCollab\TemplatedUI\MethodInvoker\MethodInvoker;
use InvalidArgumentException;
use LogicException;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use RuntimeException;
use Throwable;

class RpcServer implements RpcServerInterface
{
    private array $services = [];

    public function __construct(
        private ServiceResolverInterface $serviceResolver,
        private LoggerInterface $logger,
        private string $separator = '.',
    )
    {
        if (empty($separator)) {
            throw new InvalidArgumentException('Separator cannot be empty.');
        }
    }

    public function run(
        string $bundleName,
        string $serviceName,
        string $methodName,
        array $params = [],
    ): ResultInterface
    {
        $bundleClass = $this->bundleNameToBundleClass($bundleName);

        if (empty($bundleClass)) {
            $this->logger->error('Bundle name not provided.');

            throw new RuntimeException('Unknown bundle.');
        }

        $serviceClass = $this->serviceNameToServiceClass($bundleClass, $serviceName);

        if (empty($serviceClass)) {
            $this->logger->error(
                'Service class for "{service}" not found in {bundle}.',
                [
                    'bundle' => $this->bundleClassToBundleName($bundleClass),
                    'service' => $serviceName,
                ],
            );

            throw new RuntimeException(sprintf('Service not found in "%s" bundle.', $bundleName));
        }

        if (!$this->hasMethod($bundleClass, $serviceClass, $methodName)) {
            $this->logger->error(
                'Method "{method}" not found in service "{service}" of bundle "{bundle}".',
                [
                    'bundle' => $this->bundleClassToBundleName($bundleClass),
                    'service' => $this->serviceClassToServiceName($bundleClass, $serviceClass),
                    'method' => $methodName,
                ],
            );

            throw new RuntimeException('Unknown method.');
        }

        $service = $this->serviceResolver->getService($bundleClass, $serviceClass);

        if (empty($service)) {
            $this->logger->error(
                'Service "{service}}" not found in bundle "{bundle}".',
                [
                    [
                        'bundle' => $this->bundleClassToBundleName($bundleClass),
                        'service' => $this->serviceClassToServiceName($bundleClass, $serviceClass),
                    ],
                ],
            );

            throw new RuntimeException(
                sprintf(
                    'Service "%s" not found in bundle "%s".',
                    $this->serviceClassToServiceName($bundleClass, $serviceClass),
                    $this->bundleClassToBundleName($bundleClass),
                ),
            );
        }

        try {
            return new Success(
                (new MethodInvoker($service))->invokeMethod($methodName, $params)
            );
        } catch (Throwable $e) {
            $this->logger->error(
                'Error while running "{method}" from "{service}" of "{bundle}" bundle.',
                [
                    'bundle' => $this->bundleClassToBundleName($bundleClass),
                    'service' => $this->serviceClassToServiceName($bundleClass, $serviceClass),
                    'method' => $methodName,
                    'exception' => $e,
                ],
            );

            return new Failure($e);
        }
    }

    public function json(string $payload): JsonRpcResultInterface
    {
        if (empty($payload)) {
            throw new RuntimeException('Payload cannot be empty.');
        }

        $this->logger->debug(
            'Parsing JSON-RPC string.',
            [
                'raw_payload' => $payload,
            ],
        );

        $decodedPayload = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error(
                'JSON-RPC payload is not valid JSON.',
                [
                    'raw_payload' => $payload,
                ],
            );

            throw new RuntimeException('Invalid JSON payload.');
        }

        return $this->decodedJson($decodedPayload);
    }

    public function decodedJson(array $decodedPayload): JsonRpcResultInterface
    {
        if (!$this->isValidJsonRpc($decodedPayload)) {
            $this->logger->error(
                'Payload is not valid JSON-RPC request.',
                [
                    'decoded_payload' => $decodedPayload,
                ],
            );

            throw new RuntimeException('Invalid JSON-RPC request.');
        }

        [
            $bundleName,
            $serviceName,
            $methodName,
        ] = $this->parseMethod($decodedPayload['method']);

        return new JsonRpcResult(
            $this->run(
                $bundleName,
                $serviceName,
                $methodName,
                $decodedPayload['params'] ?? [],
            ),
            $decodedPayload['id'] ?? null,
        );
    }

    private function isValidJsonRpc(array $decodedPayload): bool
    {
        return !empty($decodedPayload['jsonrpc']) &&
            $decodedPayload['jsonrpc'] === '2.0' &&
            !empty($decodedPayload['method']) &&
            is_string($decodedPayload['method']) &&
            $this->isValidJsonRpcParams($decodedPayload);
    }

    private function isValidJsonRpcParams(array $decodedPayload): bool
    {
        if (!array_key_exists('params', $decodedPayload)) {
            return true;
        }

        return is_array($decodedPayload['params']);
    }

    private function parseMethod(string $methodString): array
    {
        if (!str_contains($methodString, $this->separator)) {
            $this->logger->error(
                'Method value "{method}" is not valid JSON-RPC method name.',
                [
                    'method' => $methodString,
                ],
            );

            throw new RuntimeException('Invalid JSON-RPC method name.');
        }

        $bits = explode($this->separator, $methodString);

        if (count($bits) !== 3) {
            $this->logger->error(
                'Method value "{method}" is not valid JSON-RPC method name.',
                [
                    'method' => $methodString,
                ],
            );

            throw new RuntimeException('Invalid JSON-RPC method name.');
        }

        foreach ($bits as $bit) {
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $bit)) {
                $this->logger->error(
                    'Method value "{method}" is not valid JSON-RPC method name.',
                    [
                        'method' => $methodString,
                    ],
                );

                throw new RuntimeException('Invalid JSON-RPC method name.');
            }
        }

        return $bits;
    }

    public function registerService(string $bundleClass, string ...$serviceClasses): void
    {
        if (empty($serviceClasses)) {
            throw new LogicException('Please provide service classes.');
        }

        $bundleName = $this->bundleClassToBundleName($bundleClass);

        if (empty($this->services[$bundleName])) {
            $this->services[$bundleName] = [];
        }

        foreach ($serviceClasses as $serviceClass) {
            [
                $serviceName,
                $serviceMethods,
            ] = $this->getMethodsFromServiceClass($bundleClass, $serviceClass);

            $this->services[$bundleName][$serviceName] = $serviceMethods;
        }
    }

    public function hasMethod(
        string $bundleClass,
        string $serviceClass,
        string $methodName,
    ): bool
    {
        $bundleName = $this->bundleClassToBundleName($bundleClass);
        $serviceName = $this->serviceClassToServiceName($bundleClass, $serviceClass);

        return !empty($this->services[$bundleName][$serviceName])
            && in_array($methodName, $this->services[$bundleName][$serviceName]);
    }

    private function getMethodsFromServiceClass(
        string $bundleClass,
        string $serviceClass,
    ): array
    {
        $reflection = new ReflectionClass($serviceClass);

        if (!$reflection->implementsInterface(ServiceInterface::class)) {
            throw new LogicException('Service class must implement ServiceInterface.');
        }

        $methodNames = [];

        foreach ($reflection->getMethods() as $method) {
            if (!$method->isPublic() || $method->isConstructor() || $method->isDestructor()) {
                continue;
            }

            $attributes = $method->getAttributes(RpcMethod::class);

            if (empty($attributes)) {
                continue;
            }

            $methodNames[] = $method->getName();
        }

        return [
            $this->serviceClassToServiceName($bundleClass, $serviceClass),
            $methodNames,
        ];
    }

    private array $bundleClassToNameMap = [];

    private function bundleClassToBundleName(string $bundleClass): string
    {
        if (empty($this->bundleClassToNameMap[$bundleClass])) {
            $reflection = new ReflectionClass($bundleClass);

            if (!$reflection->implementsInterface(BundleInterface::class)) {
                throw new LogicException('Bundle class must implement BundleInterface.');
            }

            $bits = explode('\\', $bundleClass);
            $bundleName = array_pop($bits);

            if (str_ends_with($bundleName, 'Bundle')) {
                $bundleName = substr($bundleName, 0, strlen($bundleName) - 6);
            }

            $this->bundleClassToNameMap[$bundleClass] = $bundleName;
        }

        return $this->bundleClassToNameMap[$bundleClass];
    }

    private function bundleNameToBundleClass(string $bundleName): ?string
    {
        return array_search($bundleName, $this->bundleClassToNameMap) ?: null;
    }

    private array $serviceClassToNameMap = [];

    private function serviceClassToServiceName(
        string $bundleClass,
        string $serviceClass,
    ): string
    {
        if (empty($this->serviceClassToNameMap[$bundleClass])) {
            $this->serviceClassToNameMap[$bundleClass] = [];
        }

        if (empty($this->serviceClassToNameMap[$bundleClass][$serviceClass])) {
            $bits = explode('\\', $serviceClass);
            $serviceName = array_pop($bits);

            if (str_ends_with($serviceName, 'Service')) {
                $serviceName = substr($serviceName, 0, strlen($serviceName) - 7);
            }

            $this->serviceClassToNameMap[$bundleClass][$serviceClass] = $serviceName;
        }

        return $this->serviceClassToNameMap[$bundleClass][$serviceClass];
    }

    private function serviceNameToServiceClass(
        string $bundleClass,
        string $serviceName,
    ): ?string
    {
        if (empty($this->serviceClassToNameMap[$bundleClass])) {
            return null;
        }

        return array_search($serviceName, $this->serviceClassToNameMap[$bundleClass]) ?: null;
    }
}
