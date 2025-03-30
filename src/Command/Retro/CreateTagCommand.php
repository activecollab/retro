<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro;

use ActiveCollab\Retro\CommandTrait\BundleAwareTrait;
use ActiveCollab\Retro\CommandTrait\FileManagementTrait;
use ActiveCollab\Retro\Integrate\Creator\CreatorInterface;
use ActiveCollab\TemplatedUI\Tag\Tag;
use Nette\PhpGenerator\ClassType;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class CreateTagCommand extends RetroCommand
{
    use FileManagementTrait;
    use BundleAwareTrait;

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Create tag template extension.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Name of the tag.',
            )
            ->addArgument(
                'bundle',
                InputArgument::OPTIONAL,
                'Name of the bundle where controllers should be created at.',
                'Main',
            )
            ->addOption(
                'with-template',
                '',
                InputOption::VALUE_NONE,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $extensionName = $this->getInflector()->classify($input->getArgument('name'));
            $bundle = $this->mustGetBundle($input);
            $withTemplate = $input->getOption('with-template');

            $output->writeln(
                sprintf(
                    'Building a tag <comment>%s</comment> in <comment>%s</comment> bundle...',
                    $extensionName,
                    $bundle->getName(),
                ),
            );

            $templateExtensionsPath = sprintf('%s/TemplateExtension', $bundle::PATH);
            $this->mustCreateDir($templateExtensionsPath, $output);

            $buildPath = $templateExtensionsPath;

            $templateExtensionsNamespace = sprintf(
                '\\%s\\TemplateExtension',
                ltrim($this->get(CreatorInterface::class)->getBundleNamespace($bundle), '\\'),
            );

            if ($withTemplate) {
                $buildPath = sprintf('%s/%s', $buildPath, $extensionName);
                $this->mustCreateDir($buildPath, $output);

                $templateExtensionsNamespace = sprintf(
                    '%s\\%s',
                    $templateExtensionsNamespace,
                    $extensionName,
                );
            }

            $templateName = null;

            if ($withTemplate) {
                $templateName = $this->createExtensionTemplate(
                    $buildPath,
                    $extensionName,
                    $output,
                );
            }

            $this->createExtension(
                $buildPath,
                $templateExtensionsNamespace,
                $extensionName,
                $templateName,
                $output,
            );

            return 0;
        } catch (Throwable $e) {
            return $this->abortDueToException($e, $input, $output);
        }
    }

    private function createExtension(
        string $buildPath,
        string $templateExtensionsNamespace,
        string $extensionName,
        ?string $templateName,
        OutputInterface $output,
    ): void
    {
        $extensionClassName = sprintf('%sTag', $extensionName);

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $buildPath, $extensionClassName),
            $templateExtensionsNamespace,
            [
                Tag::class,
            ],
            $this->createExtensionClass(
                $extensionName,
                $extensionClassName,
                $templateName,
            ),
            $output,
        );
    }

    private function createExtensionClass(
        string $extensionName,
        string $extensionClassName,
        ?string $templateName,
    ): string
    {
        $class = new ClassType($extensionClassName);
        $class->setExtends('Tag');

        $class
            ->addMethod('render')
            ->setBody($this->getRenderMethodBody($extensionName, $templateName))
            ->setReturnType('string');

        return (string) $class;
    }

    private function getRenderMethodBody(
        string $extensionName,
        ?string $templateName,
    ): string
    {
        if ($templateName) {
            return implode(
                "\n",
                [
                    'return $this->fetch(',
                    sprintf('    __DIR__ . %s, ', var_export(sprintf('/%s.tpl', $templateName), true)),
                    ');',
                ],
            );
        }

        return sprintf(
            'return %s;',
            var_export(
                sprintf('Rendering %s tag.', $extensionName),
                true,
            ),
        );
    }

    private function createExtensionTemplate(
        string $buildPath,
        string $extensionName,
        OutputInterface $output,
    ): string
    {
        $templateName = $this->getInflector()->tableize($extensionName);

        $this->mustCreateFile(
            sprintf('%s/%s.tpl', $buildPath, $templateName),
            sprintf("Rendering %s tag.\n", $extensionName),
            $output,
        );

        return $templateName;
    }
}
