<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\TemplatedUI\Property;

enum Width: string
{
    case AUTO = 'auto';
    case FULL = 'full';
    case SCREEN = 'screen';
    case MIN = 'min';
    case MAX = 'max';
    case FIT = 'fit';
}
