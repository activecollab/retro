<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Rpc;

use ActiveCollab\Retro\Bootstrapper\Bundle\BundleInterface;
use ActiveCollab\Retro\Service\ServiceInterface;
use LogicException;
use ReflectionClass;

class RpcServer
{
    private array $services = [];

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
            ] = $this->getMethodsFromServiceClass($serviceClass);

            $this->services[$bundleName][$serviceName] = $serviceMethods;
        }
    }

    public function hasMethod(string $bundleClass, string $serviceClass, string $methodName): bool
    {
        $bundleName = $this->bundleClassToBundleName($bundleClass);
        $serviceName = $this->getServiceNameFromServiceClass($serviceClass);

        return !empty($this->services[$bundleName][$serviceName])
            && in_array($methodName, $this->services[$bundleName][$serviceName]);
    }

    private function getMethodsFromServiceClass(string $serviceClass): array
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

            $attributes = $method->getAttributes();

            if (empty($attributes)) {
                continue;
            }

            $methodNames[] = $method->getName();
        }

        return [
            $this->getServiceNameFromServiceClass($serviceClass),
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

    private array $serviceClassToNameMap = [];

    private function getServiceNameFromServiceClass(string $serviceClass): string
    {
        if (empty($this->serviceClassToNameMap[$serviceClass])) {
            $bits = explode('\\', $serviceClass);
            $serviceName = array_pop($bits);

            if (str_ends_with($serviceName, 'Service')) {
                $serviceName = substr($serviceName, 0, strlen($serviceName) - 7);
            }

            $this->serviceClassToNameMap[$serviceClass] = $serviceName;
        }

        return $this->serviceClassToNameMap[$serviceClass];
    }
}
