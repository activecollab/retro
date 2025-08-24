<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\UI\Shoelace;

use ActiveCollab\Retro\Test\Base\TestCase;
use ActiveCollab\Retro\UI\Element\PreRendered\PreRenderedElement;
use ActiveCollab\Retro\UI\Form\Select\Element\Option;
use ActiveCollab\Retro\UI\Form\Select\Select;

class SelectTest extends TestCase
{
    public function testWillNotAcceptPreRenderedAndOtherElements(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('You cannot pass additional elements when the first argument is a pre-rendered element.');

        new Select(
            'test',
            'Test',
            null,
            new PreRenderedElement('Test content'),
            new Option('Option 1', 'option_1'),
        );
    }
}
