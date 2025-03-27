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
}
