<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test;

use ActiveCollab\Retro\Bootstrapper\ContainerBuilder\ContainerBuilder;
use ActiveCollab\Retro\Test\Base\TestCase;

class ContainerBuilderTest extends TestCase
{
    public function testContainerBuilder(): void
    {
        $definitions = (new ContainerBuilder('/var/www/app'))->buildDefinitions('1.0.0');

        $this->assertContains('/var/www/app/app/1.0.0/settings.php', $definitions);
        $this->assertContains('/var/www/app/app/1.0.0/bootstrap.php', $definitions);
        $this->assertContains('/var/www/app/app/1.0.0/events.php', $definitions);
        $this->assertContains('/var/www/app/app/1.0.0/database.php', $definitions);
        $this->assertContains('/var/www/app/app/1.0.0/structure.php', $definitions);
        $this->assertContains('/var/www/app/app/1.0.0/model.php', $definitions);
        $this->assertContains('/var/www/app/app/1.0.0/routes.php', $definitions);
        $this->assertContains('/var/www/app/app/1.0.0/auth.php', $definitions);
        $this->assertContains('/var/www/app/app/1.0.0/utils.php', $definitions);
    }
}