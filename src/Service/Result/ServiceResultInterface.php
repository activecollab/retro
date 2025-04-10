<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Service\Result;

interface ServiceResultInterface
{
    public function isSuccess(): bool;
    public function decorateUrl(string $url): string;
}
