<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\UI\Factory;

use ActiveCollab\Retro\UI\Action\GoToPage;
use ActiveCollab\Retro\UI\Action\MakeDeleteRequest;

class ActionFactory implements ActionFactoryInterface
{
    public function goToPage(string $pageUrl): GoToPage
    {
        return new GoToPage($pageUrl);
    }

    public function makeDeleteRequest(string $deleteUrl): MakeDeleteRequest
    {
        return new MakeDeleteRequest($deleteUrl);
    }
}
