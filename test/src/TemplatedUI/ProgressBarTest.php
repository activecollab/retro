<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Test\TemplatedUI;

use ActiveCollab\Retro\TemplatedUI\Indicator\ProgressBarTag;
use ActiveCollab\Retro\Test\Base\TestCase;
use InvalidArgumentException;

class ProgressBarTest extends TestCase
{
    /**
     * @dataProvider provideInvalidValues
     */
    public function testWillRejectInvalidValue(
        int $invalidValue,
    ): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be between 0 and 100.');

        (new ProgressBarTag())->render($invalidValue);
    }

    public function provideInvalidValues(): array
    {
        return [
            [-1],
            [101],
        ];
    }
}
