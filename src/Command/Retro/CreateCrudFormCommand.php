<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro;

use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\Retro\CommandTrait\BundleAwareTrait;
use ActiveCollab\Retro\CommandTrait\FileManagementTrait;
use ActiveCollab\Retro\CommandTrait\ModelAwareTrait;
use ActiveCollab\Retro\CommandTrait\ServiceContextAwareTrait;
use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\Integrate\Creator\CreatorInterface;
use ActiveCollab\Retro\Service\Result\ServiceResultInterface;
use ActiveCollab\Retro\TemplatedUI\FormExtensionTrait;
use ActiveCollab\TemplatedUI\Tag\Tag;
use Nette\PhpGenerator\ClassType;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class CreateCrudFormCommand extends RetroCommand
{
    use FileManagementTrait;
    use BundleAwareTrait;
    use ModelAwareTrait;
    use ServiceContextAwareTrait;

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Create CRUD form for the model.')
            ->addArgument(
                'model',
                InputArgument::REQUIRED,
                'Name of the model that CRUD form should be created for.',
            )
            ->addArgument(
                'bundle',
                InputArgument::OPTIONAL,
                'Name of the bundle where form tag should be created at.',
                'Main',
            )
            ->addOption(...$this->getWithinContextOptionSettings())
            ->addOption(...$this->getWithoutContextOptionSettings())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $model = $this->mustGetModel($input);
            $bundle = $this->mustGetBundle($input);
            $serviceContext = $this->getServiceContext($input);

            $extensionName = sprintf(
                '%sForm',
                $model->getEntityClassName()
            );

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

            $buildPath = sprintf('%s/%s', $buildPath, $extensionName);
            $this->mustCreateDir($buildPath, $output);

            $templateExtensionsNamespace = sprintf(
                '%s\\%s',
                $templateExtensionsNamespace,
                $extensionName,
            );

            $templateName = $this->createExtensionTemplate(
                $buildPath,
                $extensionName,
                $output,
            );

            $this->createExtension(
                $buildPath,
                $templateExtensionsNamespace,
                $extensionName,
                $templateName,
                $serviceContext,
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
        string $templateName,
        ?TypeInterface $serviceContext,
        OutputInterface $output,
    ): void
    {
        $extensionClassName = sprintf('%sTag', $extensionName);

        $useStatements = [
            ServiceResultInterface::class,
            FormDataInterface::class,
            Tag::class,
            FormExtensionTrait::class,
        ];

        if ($serviceContext) {
            $useStatements[] = sprintf(
                '%s\\%s',
                $this->get(CreatorInterface::class)->getModelNamespace(),
                $serviceContext->getEntityInterfaceName(),
            );
        }

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $buildPath, $extensionClassName),
            $templateExtensionsNamespace,
            $useStatements,
            $this->createExtensionClass(
                $extensionName,
                $extensionClassName,
                $templateName,
                $serviceContext,
            ),
            $output,
        );
    }

    private function createExtensionClass(
        string $extensionName,
        string $extensionClassName,
        string $templateName,
        ?TypeInterface $serviceContext,
    ): string
    {
        $class = new ClassType($extensionClassName);
        $class->setExtends('Tag');
        $class->addTrait('FormExtensionTrait');

        $renderMethod = $class
            ->addMethod('render')
            ->setBody($this->getRenderMethodBody($templateName, $serviceContext))
            ->setReturnType('string');

        $renderMethod
            ->addParameter('serviceProcessingResult')
            ->setType('ServiceResultInterface')
            ->setNullable();
        $renderMethod
            ->addParameter('formData')
            ->setType('FormDataInterface')
            ->setNullable();

        if ($serviceContext) {
            $renderMethod
                ->addParameter($this->getServiceContextVariableName($serviceContext))
                ->setType($serviceContext->getEntityInterfaceName());
        }

        $renderMethod->addParameter('action')->setType('string');
        $renderMethod->addParameter('buttonLabel')->setType('string');
        $renderMethod->addParameter('cancelUrl')->setType('string');
        $renderMethod->addParameter('target')
            ->setType('string')
            ->setNullable()
            ->setDefaultValue(null);

        return (string) $class;
    }

    private function getRenderMethodBody(
        string $templateName,
        ?TypeInterface $serviceContext,
    ): string
    {
        $lines = [
            'return $this->fetch(',
            sprintf('    __DIR__ . %s, ', var_export(sprintf('/%s.tpl', $templateName), true)),
            '    $this->withCommonVariables(',
            '        $serviceProcessingResult,',
            '        $formData,',
            '        $target,',
            '        [',
            "            'formAction' => \$action,",
            "            'formButtonLabel' => \$buttonLabel,",
            "            'formCancelUrl' => \$cancelUrl,",
        ];

        if ($serviceContext) {
            $lines[] = sprintf(
                "            '%s' => \$%s,",
                $this->getServiceContextVariableName($serviceContext),
                $this->getServiceContextVariableName($serviceContext),
            );
        }

        $lines = array_merge(
            $lines,
            [
                '        ],',
                '    ),',
                ');',
            ]
        );

        return implode("\n", $lines);
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