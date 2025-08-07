<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TemplateUtilsCommand extends RetroCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setDescription('List all available template utils');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            return 1;
        } catch (Exception $e) {
            return $this->abortDueToException($e, $input, $output);
        }
    }
}
