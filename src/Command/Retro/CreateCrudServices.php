<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro;

use ActiveCollab\DatabaseStructure\StructureInterface;
use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\Retro\CommandTrait\BundleAwareTrait;
use ActiveCollab\Retro\CommandTrait\DependenciesManagementTrait;
use ActiveCollab\Retro\CommandTrait\FileManagementTrait;
use ActiveCollab\Retro\CommandTrait\ModelAwareTrait;
use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\Integrate\Creator\CreatorInterface;
use ActiveCollab\Retro\Service\Result\InvalidFormData\InvalidFormData;
use ActiveCollab\Retro\Service\Result\ServiceResultInterface;
use Doctrine\Inflector\Inflector;
use Exception;
use InvalidArgumentException;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Method;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class CreateCrudServices extends RetroCommand
{
    use FileManagementTrait;
    use ModelAwareTrait;
    use BundleAwareTrait;
    use DependenciesManagementTrait;

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Create CRUD services for the model.')
            ->addArgument(
                'model',
                InputArgument::REQUIRED,
                'Name of the model that CRUD services should be created for.',
            )
            ->addArgument(
                'bundle',
                InputArgument::OPTIONAL,
                'Name of the bundle where services should be created at.',
                'Main',
            )
            ->addOption(
                'within-context',
                mode: InputOption::VALUE_REQUIRED,
                description: 'Name of the context where services should execute within.',
            )
            ->addOption(
                'without-context',
                mode: InputOption::VALUE_NONE,
                description: 'Create service without a context. This option overrides within-context option.',
            )
            ->addOption(
                'skip-autowire',
                mode: InputOption::VALUE_NONE,
                description: 'Autowire services in DI container.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $model = $this->mustGetModel($input);
            $bundle = $this->mustGetBundle($input);
            $context = $this->getContext($input);

            $output->writeln(
                sprintf(
                    'Building CRUD services for <comment>%s</comment> model in <comment>%s</comment> bundle...',
                    $model->getName(),
                    $bundle->getName(),
                ),
            );

            $modelInterfaceFqn = sprintf(
                '\\%s\\%sInterface',
                ltrim($this->get(CreatorInterface::class)->getModelNamespace(), '\\'),
                $model->getEntityClassName(),
            );

            $modelFqn = sprintf(
                '\\%s\\%s',
                ltrim($this->get(CreatorInterface::class)->getModelNamespace(), '\\'),
                $model->getEntityClassName(),
            );

            $modelServicesNamespace = sprintf(
                '\\%s\\%s',
                ltrim($this->get(CreatorInterface::class)->getServiceNamespace($bundle), '\\'),
                $model->getEntityClassName(),
            );

            $servicesPath = sprintf('%s/Service', $bundle::PATH);
            $this->mustCreateDir($servicesPath, $output);

            $modelServicesPath = sprintf('%s/%s', $servicesPath, $model->getEntityClassName());
            $this->mustCreateDir($modelServicesPath, $output);

            [
                $baseServiceInterfaceName,
                $baseServiceClassName,
                $detailsGetterName,
                $entityTypeVarName,
            ] = $this->createBaseService(
                $modelServicesPath,
                $modelServicesNamespace,
                $model,
                $output,
            );

            [
                $serviceResultsNamespace,
                $entityAddedHistoryEventInterface,
                $entityAddedHistoryEventClassName,
                $entityEditedHistoryEventInterface,
                $entityEditedHistoryEventClassName,
                $entityDeletedHistoryEventInterface,
                $entityDeletedHistoryEventClassName,
            ] = $this->createResults(
                $modelServicesPath,
                $modelServicesNamespace,
                $modelInterfaceFqn,
                $modelFqn,
                $model,
                $output,
            );

            [
                $addEntityServiceInterface,
                $addEntityServiceClass,
            ] = $this->createAddService(
                $modelServicesPath,
                $modelServicesNamespace,
                $baseServiceInterfaceName,
                $baseServiceClassName,
                $modelInterfaceFqn,
                $modelFqn,
                $detailsGetterName,
                $entityTypeVarName,
                $entityAddedHistoryEventClassName,
                sprintf('\\%s\\%s', $serviceResultsNamespace, $entityAddedHistoryEventClassName),
                $context,
                $model,
                $output,
            );

            [
                $editEntityServiceInterface,
                $editEntityServiceClass,
            ] = $this->createEditService(
                $modelServicesPath,
                $modelServicesNamespace,
                $baseServiceInterfaceName,
                $baseServiceClassName,
                $modelInterfaceFqn,
                $detailsGetterName,
                $entityTypeVarName,
                $entityEditedHistoryEventClassName,
                sprintf('\\%s\\%s', $serviceResultsNamespace, $entityEditedHistoryEventClassName),
                $context,
                $model,
                $output,
            );

            [
                $deleteEntityServiceInterface,
                $deleteEntityServiceClass,
            ] = $this->createDeleteService(
                $modelServicesPath,
                $modelServicesNamespace,
                $baseServiceInterfaceName,
                $baseServiceClassName,
                $modelInterfaceFqn,
                $entityDeletedHistoryEventClassName,
                sprintf('\\%s\\%s', $serviceResultsNamespace, $entityDeletedHistoryEventClassName),
                $context,
                $model,
                $output,
            );

            if (!$input->getOption('skip-autowire')) {
                $this->autowire(
                    $bundle,
                    sprintf('%s\\%s', $modelServicesNamespace, $addEntityServiceInterface),
                    sprintf('%s\\%s', $modelServicesNamespace, $addEntityServiceClass),
                    $output,
                );
                $this->autowire(
                    $bundle,
                    sprintf('%s\\%s', $modelServicesNamespace, $editEntityServiceInterface),
                    sprintf('%s\\%s', $modelServicesNamespace, $editEntityServiceClass),
                    $output,
                );
                $this->autowire(
                    $bundle,
                    sprintf('%s\\%s', $modelServicesNamespace, $deleteEntityServiceInterface),
                    sprintf('%s\\%s', $modelServicesNamespace, $deleteEntityServiceClass),
                    $output,
                );
            }

            return 0;
        } catch (Throwable $e) {
            return $this->abortDueToException($e, $input, $output);
        }
    }

    private function getContext(InputInterface $input): ?TypeInterface
    {
        if ($input->getOption('without-context')) {
            return null;
        }

        $context = $input->getOption('within-context');

        if (empty($context)) {
            $context = $this->get(CreatorInterface::class)->getDefaultServiceContext();
        }

        if (empty($context)) {
            return null;
        }

        if (!preg_match('/^\w+$/', $context)) {
            throw new InvalidArgumentException('Context name can contain only letters, numbers and underscores.');
        }

        return $this->get(StructureInterface::class)->getType($context);
    }

    private function createBaseService(
        string $modelServicesPath,
        string $modelServicesNamespace,
        TypeInterface $model,
        OutputInterface $output,
    ): array
    {
        $baseServiceInterfaceName = sprintf('%sServiceInterface', $model->getEntityClassName());

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $modelServicesPath, $baseServiceInterfaceName),
            $modelServicesNamespace,
            [
                $this->get(CreatorInterface::class)->getBaseService(true),
            ],
            $this->createBaseServiceInterface($baseServiceInterfaceName),
            $output,
        );

        $baseServiceClassName = sprintf('%sService', $model->getEntityClassName());
        $detailsGetterName = sprintf('mustGet%sDetails', $model->getEntityClassName());
        $entityTypeVarName = sprintf('%sType', lcfirst($model->getEntityClassName()));

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $modelServicesPath, $baseServiceClassName),
            $modelServicesNamespace,
            [
                $this->get(CreatorInterface::class)->getBaseService(),
                ServerRequestInterface::class,
                FormDataInterface::class,
            ],
            $this->createBaseServiceClass(
                $baseServiceClassName,
                $baseServiceInterfaceName,
                $detailsGetterName,
                $entityTypeVarName,
                $model,
            ),
            $output,
        );

        return [
            $baseServiceInterfaceName,
            $baseServiceClassName,
            $detailsGetterName,
            $entityTypeVarName,
        ];
    }

    private function createBaseServiceInterface(string $interfaceName): string
    {
        $interface = new InterfaceType($interfaceName);
        $interface->addExtend('ServiceInterface');

        return (string) $interface;
    }

    private function createBaseServiceClass(
        string $className,
        string $interfaceName,
        string $detailsGetterName,
        string $entityTypeVarName,
        TypeInterface $model,
    ): string
    {
        $class = new ClassType($className);
        $class->setAbstract();
        $class->setExtends('Service');
        $class->addImplement($interfaceName);

        if ($model->getPolymorph()) {
            $detailsGetterBody = [
                sprintf('$%s = $formData->extractTrimmedStringFromRequest($request, \'type\');', $entityTypeVarName),
                '',
                'return [',
                sprintf('    $%s,', $entityTypeVarName),
                '];',
            ];
        } else {
            $detailsGetterBody = [
                '// @TODO Extract and return entity attributes from request.',
                'return [',
                "    'one',",
                "    'two',",
                "    'three',",
                '];',
            ];
        }

        $detailsGetter = $class
            ->addMethod($detailsGetterName)
            ->setProtected()
            ->setReturnType('array')
            ->setBody(implode("\n", $detailsGetterBody));

        $detailsGetter->addParameter('request')->setType('ServerRequestInterface');
        $detailsGetter->addParameter('formData')->setType('FormDataInterface');

        return (string) $class;
    }

    // ---------------------------------------------------
    //  Results
    // ---------------------------------------------------

    private function createResults(
        string $modelServicesPath,
        string $modelServicesNamespace,
        $modelInterfaceFqn,
        $modelFqn,
        TypeInterface $model,
        OutputInterface $output,
    ): array
    {
        $serviceResultsPath = sprintf('%s/Result', $modelServicesPath);
        $this->mustCreateDir($serviceResultsPath, $output);

        $serviceResultsNamespace = sprintf('%s\\Result', $modelServicesNamespace);

        $baseHistoryEventInterface = sprintf('%sHistoryEventInterface', $model->getEntityClassName());

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $serviceResultsPath, $baseHistoryEventInterface),
            $serviceResultsNamespace,
            [
                HistoryEventInterface::class,
                ltrim($modelInterfaceFqn, '\\'),
            ],
            $this->createBaseHistoryEventInterface(
                $baseHistoryEventInterface,
                $model,
            ),
            $output,
        );

        $baseHistoryEventClassName = sprintf('%sHistoryEvent', $model->getEntityClassName());

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $serviceResultsPath, $baseHistoryEventClassName),
            $serviceResultsNamespace,
            [
                HistoryEvent::class,
                ltrim($modelInterfaceFqn, '\\'),
                ltrim($modelFqn, '\\'),
            ],
            $this->createBaseHistoryEventClass(
                $baseHistoryEventClassName,
                $baseHistoryEventInterface,
                $model,
            ),
            $output,
        );

        $generatedServiceResultNames = [];

        foreach (['Added', 'Edited', 'Deleted'] as $event) {
            $generatedServiceResultNames = array_merge(
                $generatedServiceResultNames,
                $this->createServiceResult(
                    $event,
                    $serviceResultsPath,
                    $serviceResultsNamespace,
                    $baseHistoryEventInterface,
                    $baseHistoryEventClassName,
                    $model,
                    $output,
                ),
            );
        }

        return array_merge(
            [
                $serviceResultsNamespace,
            ],
            $generatedServiceResultNames,
        );
    }

    private function createServiceResult(
        string $event,
        string $serviceResultsPath,
        string $serviceResultsNamespace,
        string $baseHistoryEventInterface,
        string $baseHistoryEventClassName,
        TypeInterface $model,
        OutputInterface $output,
    ): array
    {
        $entityEventHistoryEventInterface = sprintf(
            '%s%sHistoryEventInterface',
            $model->getEntityClassName(),
            $event,
        );

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $serviceResultsPath, $entityEventHistoryEventInterface),
            $serviceResultsNamespace,
            [],
            $this->createEntityServiceHistoryEventInterface(
                $entityEventHistoryEventInterface,
                $baseHistoryEventInterface,
            ),
            $output,
        );

        $entityEventHistoryEventClassName = sprintf(
            '%s%sHistoryEvent',
            $model->getEntityClassName(),
            $event,
        );

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $serviceResultsPath, $entityEventHistoryEventClassName),
            $serviceResultsNamespace,
            [],
            $this->createEntityServiceHistoryEventClass(
                $entityEventHistoryEventClassName,
                $entityEventHistoryEventInterface,
                $baseHistoryEventClassName,
            ),
            $output,
        );

        return [
            $entityEventHistoryEventInterface,
            $entityEventHistoryEventClassName,
        ];
    }

    private function createBaseHistoryEventInterface(
        string $interfaceName,
        TypeInterface $model,
    ): string
    {
        $interface = new InterfaceType($interfaceName);
        $interface->addExtend('HistoryEventInterface');

        $this->appendContextGetterMethod($model, $interface);

        return (string) $interface;
    }

    private function createBaseHistoryEventClass(
        string $className,
        string $interfaceName,
        TypeInterface $model,
    ): string
    {
        $class = new ClassType($className);
        $class->setAbstract();
        $class->setExtends('HistoryEvent');
        $class->addImplement($interfaceName);

        $this->appendContextGetterMethod(
            $model,
            $class,
            sprintf(
                'return $this->pool->getById(%s::class, $this->getMetadata()[\'%s_id\']);',
                $model->getEntityClassName(),
                $this->get(Inflector::class)->tableize($model->getEntityClassName()),
            ),
        );

        return (string) $class;
    }

    private function appendContextGetterMethod(
        TypeInterface $model,
        ClassType|InterfaceType $appendTo,
        string $body = null,
    ): void
    {
        $contextGetterMethod = $appendTo
            ->addMethod(sprintf('get%s', $model->getEntityClassName()))
            ->setReturnType(sprintf('?%s', $model->getEntityInterfaceName()));

        if ($body) {
            $contextGetterMethod->setBody($body);
        }
    }

    private function createEntityServiceHistoryEventInterface(
        string $interfaceName,
        string $baseHistoryEventInterface,
    ): string
    {
        $interface = new InterfaceType($interfaceName);
        $interface->addExtend($baseHistoryEventInterface);

        return (string) $interface;
    }

    private function createEntityServiceHistoryEventClass(
        string $className,
        string $interfaceName,
        string $baseHistoryClassName,
    ): string
    {
        $class = new ClassType($className);
        $class->setExtends($baseHistoryClassName);
        $class->addImplement($interfaceName);

        return (string) $class;
    }

    // ---------------------------------------------------
    //  Add service
    // ---------------------------------------------------

    public function createAddService(
        string $modelServicesPath,
        string $modelServicesNamespace,
        string $baseServiceInterfaceName,
        string $baseServiceClassName,
        string $modelInterfaceFqn,
        string $modelFqn,
        string $detailsGetterName,
        string $entityTypeVarName,
        string $entityAddedHistoryEventClassName,
        string $entityAddedHistoryEventFqn,
        ?TypeInterface $context,
        TypeInterface $model,
        OutputInterface $output,
    ): array
    {
        $addEntityServiceInterface = sprintf(
            'Add%sServiceInterface',
            $model->getEntityClassName(),
        );

        $interfaceUseStatements = [
            ServerRequestInterface::class,
            FormDataInterface::class,
            $this->get(CreatorInterface::class)->getBaseUser(true),
            ServiceResultInterface::class,
        ];

        if ($context) {
            $interfaceUseStatements[] = sprintf(
                '%s\\%s',
                ltrim($this->get(CreatorInterface::class)->getModelNamespace(), '\\'),
                $context->getEntityInterfaceName(),
            );
        }

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $modelServicesPath, $addEntityServiceInterface),
            $modelServicesNamespace,
            $interfaceUseStatements,
            $this->createAddEntityServiceInterface(
                $addEntityServiceInterface,
                $baseServiceInterfaceName,
                $context,
            ),
            $output,
        );

        $addEntityServiceClass = sprintf('Add%sService', $model->getEntityClassName());

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $modelServicesPath, $addEntityServiceClass),
            $modelServicesNamespace,
            array_merge(
                $interfaceUseStatements,
                array_merge(
                    [
                        ltrim($entityAddedHistoryEventFqn, '\\'),
                        ltrim($modelInterfaceFqn, '\\'),
                        InvalidFormData::class,
                        Exception::class,
                    ],
                    $model->getPolymorph()
                        ? []
                        : [
                            ltrim($modelFqn, '\\'),
                        ],
                ),
            ),
            $this->createAddEntityServiceClass(
                $addEntityServiceClass,
                $addEntityServiceInterface,
                $baseServiceClassName,
                $detailsGetterName,
                $entityTypeVarName,
                $entityAddedHistoryEventClassName,
                $context,
                $model,
            ),
            $output,
        );

        return [
            $addEntityServiceInterface,
            $addEntityServiceClass,
        ];
    }

    private function createAddEntityServiceInterface(
        string $interfaceName,
        string $baseServiceInterface,
        ?TypeInterface $context,
    ): string
    {
        $interface = new InterfaceType($interfaceName);
        $interface->addExtend($baseServiceInterface);

        $this->appendProcessRequestMethodForAddService(
            $interface,
            $context,
        );

        return (string) $interface;
    }

    private function createAddEntityServiceClass(
        string $className,
        string $interfaceName,
        string $baseServiceClassName,
        string $detailsGetterName,
        string $entityTypeVarName,
        string $entityAddedHistoryEventClassName,
        ?TypeInterface $context,
        TypeInterface $model,
    ): string
    {
        $class = new ClassType($className);
        $class->setExtends($baseServiceClassName);
        $class->addImplement($interfaceName);

        $producedEntityVarName = lcfirst($model->getEntityClassName());

        /** @var Inflector $inflector */
        $inflector = $this->getContainer()->get(Inflector::class);

        $transactionCallbackImports = array_filter(
            array_merge(
                [
                    '$request',
                    '$formData',
                ],
                [
                    $context ? sprintf('$%s', $this->getContextVariableName($context)) : '',
                ],
                [
                    '$authenticatedUser',
                ],
            ),
        );

        $processRequestBody = array_merge(
            [
                'return $this->withinTransaction(',
                sprintf('    function () use (%s) {', implode(', ', $transactionCallbackImports)),
            ],
            $this->getMustGetDetailsCallLines($detailsGetterName, $entityTypeVarName, $model),
            [
                '',
                '        if ($formData->hasErrors()) {',
                '            return new InvalidFormData($formData);',
                '        }',
                '',
                '        try {',
                sprintf('            $%s = $this->pool->produce(', $producedEntityVarName),
                $model->getPolymorph()
                    ? sprintf('                $%s,', $entityTypeVarName)
                    : sprintf('                %s::class,', $model->getEntityClassName()),
            ],
            $this->getAddEntityAttributeLines($model),
            [
                '            );',
                '',
                sprintf('            return $this->serviceResultFactory->%s->record(', $this->getRecorderFactoryCall($context)),
                sprintf('                %s::class,', $entityAddedHistoryEventClassName),
                '                $authenticatedUser,',
                '                [',
                sprintf('                    \'%s_id\' => $%s->getId(),', $inflector->tableize($model->getEntityClassName()), $producedEntityVarName),
                '                ],',
                '            );',
                '        } catch (Exception $e) {',
                '            return $this->processingExceptionToResult($e, $formData);',
                '        }',
                '    },',
                '    $formData,',
                ');',
            ],
        );

        $this->appendProcessRequestMethodForAddService(
            $class,
            $context,
            implode("\n", $processRequestBody),
        );

        return (string) $class;
    }

    private function getMustGetDetailsCallLines(
        string $detailsGetterName,
        string $entityTypeVarName,
        TypeInterface $model,
    ): array
    {
        if ($model->getPolymorph()) {
            return [
                '        [',
                sprintf('            $%s,', $entityTypeVarName),
                sprintf('        ] = $this->%s($request, $formData);', $detailsGetterName),
            ];
        }

        return [
            '        [',
            '            // @TODO Set proper list of entity attributes once you implement details method.',
            '            $attribute_1,',
            '            $attribute_2,',
            '            $attribute_3,',
            sprintf('        ] = $this->%s($request, $formData);', $detailsGetterName),
        ];
    }

    private function getAddEntityAttributeLines(TypeInterface $model): array
    {
        if ($model->getPolymorph()) {
            return [
                '                [',
                '                ],',
            ];
        }

        return [
            '                [',
            '                    \'attribute_1\' => $attribute_1,',
            '                    \'attribute_2\' => $attribute_2,',
            '                    \'attribute_3\' => $attribute_3,',
            '                ],',
        ];
    }

    private function appendProcessRequestMethodForAddService(
        ClassType|InterfaceType $appendTo,
        ?TypeInterface $context,
        string $body = null,
    ): void
    {
        $processMethod = $appendTo
            ->addMethod('processRequest')
            ->setReturnType('ServiceResultInterface');

        $processMethod->addParameter('request')->setType('ServerRequestInterface');
        $processMethod->addParameter('formData')->setType('FormDataInterface');

        if ($context) {
            $this->appendContextArgument($processMethod, $context);
        }
        $processMethod->addParameter('authenticatedUser')->setType('UserInterface');

        if ($body) {
            $processMethod->setBody($body);
        }
    }

    // ---------------------------------------------------
    //  Edit service
    // ---------------------------------------------------

    public function createEditService(
        string $modelServicesPath,
        string $modelServicesNamespace,
        string $baseServiceInterfaceName,
        string $baseServiceClassName,
        string $modelInterfaceFqn,
        string $detailsGetterName,
        string $entityTypeVarName,
        string $entityEditedHistoryEventClassName,
        string $entityEditedHistoryEventFqn,
        ?TypeInterface $context,
        TypeInterface $model,
        OutputInterface $output,
    ): array
    {
        $editEntityServiceInterface = sprintf(
            'Edit%sServiceInterface',
            $model->getEntityClassName(),
        );

        $interfaceUseStatements = [
            ServerRequestInterface::class,
            FormDataInterface::class,
            $this->get(CreatorInterface::class)->getBaseUser(true),
            ServiceResultInterface::class,
            ltrim($modelInterfaceFqn, '\\'),
        ];

        if ($context) {
            $interfaceUseStatements[] = sprintf(
                '%s\\%s',
                ltrim($this->get(CreatorInterface::class)->getModelNamespace(), '\\'),
                $context->getEntityInterfaceName(),
            );
        }

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $modelServicesPath, $editEntityServiceInterface),
            $modelServicesNamespace,
            $interfaceUseStatements,
            $this->createEditEntityServiceInterface(
                $editEntityServiceInterface,
                $baseServiceInterfaceName,
                $context,
                $model,
            ),
            $output,
        );

        $editEntityServiceClass = sprintf('Edit%sService', $model->getEntityClassName());

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $modelServicesPath, $editEntityServiceClass),
            $modelServicesNamespace,
            array_merge(
                $interfaceUseStatements,
                [
                    ltrim($entityEditedHistoryEventFqn, '\\'),
                    InvalidFormData::class,
                    Exception::class,
                ],
            ),
            $this->createEditEntityServiceClass(
                $editEntityServiceClass,
                $editEntityServiceInterface,
                $baseServiceClassName,
                $detailsGetterName,
                $entityTypeVarName,
                $entityEditedHistoryEventClassName,
                $context,
                $model,
            ),
            $output,
        );

        return [
            $editEntityServiceInterface,
            $editEntityServiceClass,
        ];
    }

    private function createEditEntityServiceInterface(
        string $interfaceName,
        string $baseServiceInterface,
        ?TypeInterface $context,
        TypeInterface $model,
    ): string
    {
        $interface = new InterfaceType($interfaceName);
        $interface->addExtend($baseServiceInterface);

        $this->appendProcessRequestMethodForEditService(
            $interface,
            $context,
            $model,
        );

        return (string) $interface;
    }

    private function createEditEntityServiceClass(
        string $className,
        string $interfaceName,
        string $baseServiceClassName,
        string $detailsGetterName,
        string $entityTypeVarName,
        string $entityEditedHistoryEventClassName,
        ?TypeInterface $context,
        TypeInterface $model,
    ): string
    {
        $class = new ClassType($className);
        $class->setExtends($baseServiceClassName);
        $class->addImplement($interfaceName);

        $entityVarName = lcfirst($model->getEntityClassName());

        /** @var Inflector $inflector */
        $inflector = $this->getContainer()->get(Inflector::class);

        $transactionCallbackImports = array_filter(
            array_merge(
                [
                    '$request',
                    '$formData',
                ],
                [
                    $context ? sprintf('$%s', $this->getContextVariableName($context)) : '',
                ],
                [
                    sprintf('$%s', $entityVarName),
                    '$authenticatedUser',
                ],
            ),
        );

        $processRequestBody = array_merge(
            [
                'return $this->withinTransaction(',
                sprintf('    function () use (%s) {', implode(', ', $transactionCallbackImports)),
            ],
            $this->getMustGetDetailsCallLines($detailsGetterName, $entityTypeVarName, $model),
            [
                '',
                '        if ($formData->hasErrors()) {',
                '            return new InvalidFormData($formData);',
                '        }',
                '',
                '        try {',
                sprintf('            $%s = $this->pool->modify(', $entityVarName),
                sprintf('                $%s,', $entityVarName),
            ],
            $this->getEditEntityAttributeLines($entityTypeVarName, $model),
            [
                '            );',
                '',
                sprintf('            return $this->%s->record(', $this->getRecorderFactoryCall($context)),
                sprintf('                %s::class,', $entityEditedHistoryEventClassName),
                '                $authenticatedUser,',
                '                [',
                sprintf('                    \'%s_id\' => $%s->getId(),', $inflector->tableize($model->getEntityClassName()), $entityVarName),
                '                ],',
                '            );',
                '        } catch (Exception $e) {',
                '            return $this->processingExceptionToResult($e, $formData);',
                '        }',
                '    },',
                '    $formData,',
                ');',
            ],
        );

        $this->appendProcessRequestMethodForEditService(
            $class,
            $context,
            $model,
            implode("\n", $processRequestBody),
        );

        return (string) $class;
    }

    private function getEditEntityAttributeLines(
        string $entityTypeVarName,
        TypeInterface $model,
    ): array
    {
        if ($model->getPolymorph()) {
            return [
                '                [',
                sprintf('                    \'type\' => $%s,', $entityTypeVarName),
                '                ],',
            ];
        }

        return [
            '                [',
            '                    \'attribute_1\' => $attribute_1,',
            '                    \'attribute_2\' => $attribute_2,',
            '                    \'attribute_3\' => $attribute_3,',
            '                ],',
        ];
    }

    private function appendProcessRequestMethodForEditService(
        ClassType|InterfaceType $appendTo,
        ?TypeInterface $context,
        TypeInterface $model,
        string $body = null,
    ): void
    {
        $processMethod = $appendTo
            ->addMethod('processRequest')
            ->setReturnType('ServiceResultInterface');

        $processMethod->addParameter('request')->setType('ServerRequestInterface');
        $processMethod->addParameter('formData')->setType('FormDataInterface');

        if ($context) {
            $this->appendContextArgument($processMethod, $context);
        }

        $processMethod->addParameter(lcfirst($model->getEntityClassName()))->setType($model->getEntityInterfaceName());
        $processMethod->addParameter('authenticatedUser')->setType('UserInterface');

        if ($body) {
            $processMethod->setBody($body);
        }
    }

    // ---------------------------------------------------
    //  Delete service
    // ---------------------------------------------------

    public function createDeleteService(
        string $modelServicesPath,
        string $modelServicesNamespace,
        string $baseServiceInterfaceName,
        string $baseServiceClassName,
        string $modelInterfaceFqn,
        string $entityDeletedHistoryEventClassName,
        string $entityDeletedHistoryEventFqn,
        ?TypeInterface $context,
        TypeInterface $model,
        OutputInterface $output,
    ): array
    {
        $deleteEntityServiceInterface = sprintf(
            'Delete%sServiceInterface',
            $model->getEntityClassName(),
        );

        $interfaceUseStatements = [
            ServerRequestInterface::class,
            FormDataInterface::class,
            $this->get(CreatorInterface::class)->getBaseUser(true),
            ServiceResultInterface::class,
            ltrim($modelInterfaceFqn, '\\'),
        ];

        if ($context) {
            $interfaceUseStatements[] = sprintf(
                '%s\\%s',
                ltrim($this->get(CreatorInterface::class)->getModelNamespace(), '\\'),
                $context->getEntityInterfaceName(),
            );
        }

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $modelServicesPath, $deleteEntityServiceInterface),
            $modelServicesNamespace,
            $interfaceUseStatements,
            $this->createDeleteEntityServiceInterface(
                $deleteEntityServiceInterface,
                $baseServiceInterfaceName,
                $context,
                $model,
            ),
            $output,
        );

        $deleteEntityServiceClass = sprintf('Delete%sService', $model->getEntityClassName());

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $modelServicesPath, $deleteEntityServiceClass),
            $modelServicesNamespace,
            array_merge(
                $interfaceUseStatements,
                [
                    ltrim($entityDeletedHistoryEventFqn, '\\'),
                ],
            ),
            $this->createDeleteEntityServiceClass(
                $deleteEntityServiceClass,
                $deleteEntityServiceInterface,
                $baseServiceClassName,
                $entityDeletedHistoryEventClassName,
                $context,
                $model,
            ),
            $output,
        );

        return [
            $deleteEntityServiceInterface,
            $deleteEntityServiceClass,
        ];
    }

    private function createDeleteEntityServiceInterface(
        string $interfaceName,
        string $baseServiceInterface,
        ?TypeInterface $context,
        TypeInterface $model,
    ): string
    {
        $interface = new InterfaceType($interfaceName);
        $interface->addExtend($baseServiceInterface);

        $this->appendProcessRequestMethodForDeleteService(
            $interface,
            $context,
            $model,
        );

        return (string) $interface;
    }

    private function createDeleteEntityServiceClass(
        string $className,
        string $interfaceName,
        string $baseServiceClassName,
        string $entityDeletedHistoryEventClassName,
        ?TypeInterface $context,
        TypeInterface $model,
    ): string
    {
        $class = new ClassType($className);
        $class->setExtends($baseServiceClassName);
        $class->addImplement($interfaceName);

        $idVarName = sprintf('%sId', lcfirst(($model->getEntityClassName())));
        $entityVarName = lcfirst($model->getEntityClassName());

        /** @var Inflector $inflector */
        $inflector = $this->getContainer()->get(Inflector::class);

        $transactionCallbackImports = array_filter(
            array_merge(
                [
                    $context ? sprintf('$%s', $this->getContextVariableName($context)) : '',
                ],
                [
                    sprintf('$%s', $entityVarName),
                    '$authenticatedUser',
                ],
            ),
        );

        $processRequestBody = [
            'return $this->withinTransaction(',
            sprintf('    function () use (%s) {', implode(', ', $transactionCallbackImports)),
            sprintf('        $%s = $%s->getId();', $idVarName, $entityVarName),
            '',
            sprintf('        $%s->delete();', $entityVarName),
            '',
            sprintf('            return $this->%s->record(', $this->getRecorderFactoryCall($context)),
            sprintf('            %s::class,', $entityDeletedHistoryEventClassName),
            '            $authenticatedUser,',
            '            [',
            sprintf('                \'%s_id\' => $%s,', $inflector->tableize($model->getEntityClassName()), $idVarName),
            '            ],',
            '        );',
            '    },',
            '    $formData,',
            ');',
        ];

        $this->appendProcessRequestMethodForDeleteService(
            $class,
            $context,
            $model,
            implode("\n", $processRequestBody),
        );

        return (string) $class;
    }

    private function appendProcessRequestMethodForDeleteService(
        ClassType|InterfaceType $appendTo,
        ?TypeInterface $context,
        TypeInterface $model,
        string $body = null,
    ): void
    {
        $processMethod = $appendTo
            ->addMethod('processRequest')
            ->setReturnType('ServiceResultInterface');

        $processMethod->addParameter('request')->setType('ServerRequestInterface');
        $processMethod->addParameter('formData')->setType('FormDataInterface');

        if ($context) {
            $this->appendContextArgument($processMethod, $context);
        }

        $processMethod->addParameter(lcfirst($model->getEntityClassName()))->setType($model->getEntityInterfaceName());
        $processMethod->addParameter('authenticatedUser')->setType('UserInterface');

        if ($body) {
            $processMethod->setBody($body);
        }
    }

    private ?string $contextVariableName = null;

    private function getContextVariableName(TypeInterface $context): string
    {
        if ($this->contextVariableName === null) {
            $this->contextVariableName = lcfirst($context->getEntityClassName());
        }

        return $this->contextVariableName;
    }

    private function appendContextArgument(Method $method, TypeInterface $context): void
    {
        $method->addParameter($this->getContextVariableName($context))->setType($context->getEntityInterfaceName());
    }

    private function getRecorderFactoryCall(?TypeInterface $context): string
    {
        if (empty($context)) {
            return 'withoutContext()';
        }

        return sprintf('withinContext($%s)', $this->getContextVariableName($context));
    }
}
