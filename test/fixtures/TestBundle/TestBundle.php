<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\Fixtures\TestBundle;

use ActiveCollab\Retro\Bootstrapper\Bundle\Bundle;

class TestBundle extends Bundle
{
    public const PATH = __DIR__;
    public const DEPENDENCIES = __DIR__ . '/dependencies.php';
}
