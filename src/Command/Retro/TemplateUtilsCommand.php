<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro;

use ActiveCollab\TemplatedUI\Util\TemplateUtilsResolverInterface;
use Exception;
use Symfony\Component\Console\Helper\Table;
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
            if (!$this->has(TemplateUtilsResolverInterface::class)) {
                $output->writeln('This application does not provide any template utils automatically.');
                return 0;
            }

            $templateUtils = $this->get(TemplateUtilsResolverInterface::class)->resolve();

            if (empty($templateUtils)) {
                $output->writeln('This application does not provide any template utils automatically.');
                return 0;
            }

            $table = new Table($output);
            $table->setHeaders(
                [
                    'Template Variable',
                    'Service ID',
                ],
            );

            foreach ($templateUtils as $variableName => $service) {
                $table->addRow(
                    [
                        $variableName,
                        get_class($service),
                    ],
                );
            }

            $table->render();

            return 1;
        } catch (Exception $e) {
            return $this->abortDueToException($e, $input, $output);
        }
    }
}
