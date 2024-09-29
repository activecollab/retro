<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test;

use ActiveCollab\Retro\Bootstrapper\Bundle\Manager\BundleManager;
use ActiveCollab\Retro\Test\Base\TestCase;
use ActiveCollab\Retro\Test\Fixtures\TestBundle\TestBundle;
use LogicException;

class BundleManagerTest extends TestCase
{
    public function testWillRejectUnknownBundleClasses(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Bundle for class "unknown class" not found.');

        (new BundleManager(TestBundle::class))->getByClassName('unknown class');
    }

    public function testWillGetBundleByClassName(): void
    {
        $this->assertInstanceOf(
            TestBundle::class,
            (new BundleManager(TestBundle::class))->getByClassName(TestBundle::class),
        );
    }

    public function testWillRejectUnknownBundle(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Bundle "Unknown Bundle" not found.');

        (new BundleManager(TestBundle::class))->getByName('Unknown Bundle');
    }

    public function testWillGetBundleByShortName(): void
    {
        $this->assertInstanceOf(
            TestBundle::class,
            (new BundleManager(TestBundle::class))->getByName('Test'),
        );
    }
}
