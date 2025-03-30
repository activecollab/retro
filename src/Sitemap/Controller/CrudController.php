<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Sitemap\Controller;

use ActiveCollab\Retro\FormData\Factory\FormDataFactoryInterface;
use ActiveCollab\Sitemap\NodeMiddleware\Controller\Controller;
use ActiveCollab\TemplatedUI\ExtensionInterface;
use ActiveCollab\TemplatedUI\Integrate\SmartyFactoryInterface;
use RuntimeException;

abstract class CrudController extends Controller
{
    public function __construct(
        private FormDataFactoryInterface $formDataFactory,
        string $absoluteMiddlewarePath,
    )
    {
        parent::__construct($absoluteMiddlewarePath);
    }

    protected function getFormDataFactory(): FormDataFactoryInterface
    {
        return $this->formDataFactory;
    }

    /**
     * @template TClassName
     * @param  class-string<TClassName> $extensionType
     * @return TClassName
     */
    protected function getExtension(string $extensionType): ?ExtensionInterface
    {
        return $this->get(SmartyFactoryInterface::class)->getExtension($extensionType);
    }

    /**
     * @template TClassName
     * @param  class-string<TClassName> $extensionType
     * @return TClassName
     */
    protected function mustGetExtension(string $extensionType): ExtensionInterface
    {
        $extension = $this->getExtension($extensionType);

        if (empty($extension)) {
            throw new RuntimeException(sprintf('Extension "%s" not found.', $extensionType));
        }

        return $extension;
    }
}
