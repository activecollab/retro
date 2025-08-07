<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\UI\Action;

use ActiveCollab\Retro\Test\Base\TestCase;
use ActiveCollab\Retro\UI\Action\MakeDeleteRequest;

class MakeDeleteActionRequestTest extends TestCase
{
    public function testWillHaveTraits(): void
    {
        $makeDeleteRequest = (new MakeDeleteRequest('https://example.com/delete-url'))
            ->confirm('Are you sure you want to delete this?')
            ->swap('outerHTML')
            ->target('#target-element');

        $this->assertSame('Are you sure you want to delete this?', $makeDeleteRequest->getConfirm());
        $this->assertSame('outerHTML', $makeDeleteRequest->getSwap());
        $this->assertSame('#target-element', $makeDeleteRequest->getTarget());
    }
}
