<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Form;

use ActiveCollab\Retro\Bootstrapper\Metadata\EnvironmentInterface;
use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\Service\Result\InvalidFormData\InvalidFormDataInterface;
use ActiveCollab\Retro\Service\Result\RequestProcessingFailed\RequestProcessingFailedInterface;
use ActiveCollab\Retro\Service\Result\ServiceResultInterface;
use ActiveCollab\Retro\TemplatedUI\ComponentIdResolver\ComponentIdResolverInterface;
use ActiveCollab\Retro\TemplatedUI\Decorator\DecoratorInterface;
use ActiveCollab\TemplatedUI\MethodInvoker\CatchAllParameters\CatchAllParametersInterface;
use ActiveCollab\TemplatedUI\WrapContentBlock\WrapContentBlock;
use Exception;
use Psr\Log\LoggerInterface;
use Throwable;

class FormBlock extends WrapContentBlock
{
    public function __construct(
        private ComponentIdResolverInterface $componentIdResolver,
        private DecoratorInterface $decorator,
        private EnvironmentInterface $environment,
        private LoggerInterface $logger,
    )
    {
    }

    public function render(
        string $action,
        string $content,
        string $id = null,
        string $target = null,
        string $swap = null,
        bool $autocomplete = false,
        bool $pushUrl = true,
        ServiceResultInterface $serviceProcessingResult = null,
        FormDataInterface $formData = null,
        CatchAllParametersInterface $catchAllParameters = null,
    ): string
    {
        $formId = $id ?? $this->componentIdResolver->getUniqueId('form');

        if ($formData === null && $serviceProcessingResult && method_exists($serviceProcessingResult, 'getFormData')) {
            $formData = $serviceProcessingResult->getFormData();
        }

        $formAttributes = [
            'id' => $formId,
            'action' => $action,
            'method' => 'POST',
            'autocomplete' => $autocomplete ? 'on' : 'off',
        ];

        if ($target) {
            $formAttributes['hx-target'] = $target;
            $formAttributes['hx-target-4xx'] = $target;
        }

        if ($swap) {
            $formAttributes['hx-swap'] = $swap;
        }

        if (!$pushUrl) {
            $formAttributes['hx-push-url'] = 'false';
        }

        return sprintf(
            '%s%s%s%s',
            $this->renderErrorsHeader($formData, $serviceProcessingResult),
            $this->openHtmlTag(
                'form',
                array_merge(
                    $catchAllParameters?->getParameters() ?? [],
                    $formAttributes,
                ),
            ),
            $content,
            $this->closeHtmlTag('form'),
        );
    }

    private function renderErrorsHeader(
        FormDataInterface $formData = null,
        ServiceResultInterface $serviceProcessingResult = null,
    ): string
    {
        if (empty($serviceProcessingResult) && empty($formData)) {
            return '';
        }

        if ($serviceProcessingResult instanceof InvalidFormDataInterface) {
            return $this->renderValidationErrorsHeader($serviceProcessingResult->getFormData());
        }

        if ($serviceProcessingResult instanceof RequestProcessingFailedInterface) {
            return sprintf(
                '<div class="my-4"><p class="%s">Error: %s</p></div>',
                $this->decorator->getAlertColor(),
                $this->sanitizeForHtml(
                    $this->getMessageFromOperationFailedReason(
                        $serviceProcessingResult->getReason(),
                    ),
                ),
            );
        }

        return '';
    }

    private function renderValidationErrorsHeader(FormDataInterface $formData): string
    {
        if (!$formData->hasErrors()) {
            return '';
        }

        $result = sprintf(
            '<div class="my-4"><p class="%s">Please correct the following errors:</p><ul>',
            $this->decorator->getAlertColor(),
        );

        foreach ($formData->getErrors() as $fieldErrors) {
            foreach ($fieldErrors as $fieldError) {
                $result .= sprintf(
                    '<li class="list-disc ml-4 py-2 text-red-500">%s</li>',
                    $this->sanitizeForHtml($fieldError),
                );
            }
        }

        return $result . '</ul></div>';
    }

    private function getMessageFromOperationFailedReason(Throwable $reason): string
    {
        if ($reason instanceof Exception || $this->environment->isDevelopment()) {
            return $reason->getMessage();
        }

        $this->logger->error(
            sprintf('Client facing exception %s', $reason->getMessage()),
            [
                'exception' => $reason,
            ],
        );

        return 'Operation failed. This error has been logged and reported to administrators.';
    }
}
