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
use ActiveCollab\Retro\CommandTrait\DependenciesManagementTrait;
use ActiveCollab\Retro\CommandTrait\FileManagementTrait;
use ActiveCollab\Retro\CommandTrait\ModelAwareTrait;
use ActiveCollab\Retro\FormData\FormDataInterface;
use ActiveCollab\Retro\Service\Result\InvalidFormData\InvalidFormData;
use ActiveCollab\Retro\Service\Result\ServiceResultInterface;
use Doctrine\Inflector\Inflector;
use Exception;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use PhpCloudOrg\Feud\Bootstrap\Service\Service;
use PhpCloudOrg\Feud\Bootstrap\Service\ServiceInterface;
use PhpCloudOrg\Feud\Command\DevCommand\Services\Command;
use PhpCloudOrg\Feud\Model\AccountInterface;
use PhpCloudOrg\Feud\Model\HistoryEvent;
use PhpCloudOrg\Feud\Model\HistoryEventInterface;
use PhpCloudOrg\Feud\Model\UserInterface;
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
                'skip-autowire',
                '',
                InputOption::VALUE_NONE,
                'Autowire services.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $model = $this->mustGetModel($input);
            $bundle = $this->mustGetBundle($input);

            $output->writeln(
                sprintf(
                    'Building CRUD services for <comment>%s</comment> model in <comment>%s</comment> bundle...',
                    $model->getName(),
                    $bundle->getName(),
                ),
            );

            $modelInterfaceFqn = sprintf(
                '\\PhpCloudOrg\\Feud\\Model\\%sInterface',
                $model->getEntityClassName(),
            );

            $modelFqn = sprintf(
                '\\PhpCloudOrg\\Feud\\Model\\%s',
                $model->getEntityClassName(),
            );

            $modelServicesNamespace = sprintf(
                '\\PhpCloudOrg\\Feud\\Bundles\\%s\\Service\\%s',
                $bundle->getName(),
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
                ServiceInterface::class,
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
                Service::class,
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

        /** @var Inflector $inflector */
        $inflector = $this->getContainer()->get(Inflector::class);

        $this->appendContextGetterMethod(
            $model,
            $class,
            sprintf(
                'return $this->pool->getById(%s::class, $this->getMetadata()[\'%s_id\']);',
                $model->getEntityClassName(),
                $inflector->tableize($model->getEntityClassName()),
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
            UserInterface::class,
            ServiceResultInterface::class,
            AccountInterface::class,
        ];

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $modelServicesPath, $addEntityServiceInterface),
            $modelServicesNamespace,
            $interfaceUseStatements,
            $this->createAddEntityServiceInterface(
                $addEntityServiceInterface,
                $baseServiceInterfaceName,
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
    ): string
    {
        $interface = new InterfaceType($interfaceName);
        $interface->addExtend($baseServiceInterface);

        $this->appendProcessRequestMethodForAddService($interface);

        return (string) $interface;
    }

    private function createAddEntityServiceClass(
        string $className,
        string $interfaceName,
        string $baseServiceClassName,
        string $detailsGetterName,
        string $entityTypeVarName,
        string $entityAddedHistoryEventClassName,
        TypeInterface $model,
    ): string
    {
        $class = new ClassType($className);
        $class->setExtends($baseServiceClassName);
        $class->addImplement($interfaceName);

        $producedEntityVarName = lcfirst($model->getEntityClassName());

        /** @var Inflector $inflector */
        $inflector = $this->getContainer()->get(Inflector::class);

        $processRequestBody = array_merge(
            [
                'return $this->withinTransaction(',
                '    function () use ($request, $formData, $account, $authenticatedUser) {',
            ],
            $this->getMustGetDetailsCallLines($detailsGetterName, $entityTypeVarName, $model),
            [
                '',
                '        if ($formData->hasErrors()) {',
                '            return new InvalidFormData($formData);',
                '        }',
                '',
                '        try {',
                sprintf('            /** @var %s $%s */', $model->getEntityInterfaceName(), $producedEntityVarName),
                sprintf('            $%s = $this->pool->produce(', $producedEntityVarName),
                $model->getPolymorph()
                    ? sprintf('                $%s,', $entityTypeVarName)
                    : sprintf('                %s::class,', $model->getEntityClassName()),
            ],
            $this->getAddEntityAttributeLines($model),
            [
                '            );',
                '',
                '            return $this->historyEventsFactory->createHistoryEvent(',
                sprintf('                %s::class,', $entityAddedHistoryEventClassName),
                '                $authenticatedUser,',
                '                $account,',
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
        string $body = null,
    ): void
    {
        $processMethod = $appendTo
            ->addMethod('processRequest')
            ->setReturnType('ServiceResultInterface');

        $processMethod->addParameter('request')->setType('ServerRequestInterface');
        $processMethod->addParameter('formData')->setType('FormDataInterface');
        $processMethod->addParameter('account')->setType('AccountInterface');
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
            UserInterface::class,
            ServiceResultInterface::class,
            AccountInterface::class,
            ltrim($modelInterfaceFqn, '\\'),
        ];

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $modelServicesPath, $editEntityServiceInterface),
            $modelServicesNamespace,
            $interfaceUseStatements,
            $this->createEditEntityServiceInterface(
                $editEntityServiceInterface,
                $baseServiceInterfaceName,
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
        TypeInterface $model,
    ): string
    {
        $interface = new InterfaceType($interfaceName);
        $interface->addExtend($baseServiceInterface);

        $this->appendProcessRequestMethodForEditService($interface, $model);

        return (string) $interface;
    }

    private function createEditEntityServiceClass(
        string $className,
        string $interfaceName,
        string $baseServiceClassName,
        string $detailsGetterName,
        string $entityTypeVarName,
        string $entityEditedHistoryEventClassName,
        TypeInterface $model,
    ): string
    {
        $class = new ClassType($className);
        $class->setExtends($baseServiceClassName);
        $class->addImplement($interfaceName);

        $entityVarName = lcfirst($model->getEntityClassName());

        /** @var Inflector $inflector */
        $inflector = $this->getContainer()->get(Inflector::class);

        $processRequestBody = array_merge(
            [
                'return $this->withinTransaction(',
                sprintf('    function () use ($request, $formData, $account, $%s, $authenticatedUser) {', $entityVarName),
            ],
            $this->getMustGetDetailsCallLines($detailsGetterName, $entityTypeVarName, $model),
            [
                '',
                '        if ($formData->hasErrors()) {',
                '            return new InvalidFormData($formData);',
                '        }',
                '',
                '        try {',
                sprintf('            /** @var %s $%s */', $model->getEntityInterfaceName(), $entityVarName),
                sprintf('            $%s = $this->pool->modify(', $entityVarName),
                sprintf('                $%s,', $entityVarName),
            ],
            $this->getEditEntityAttributeLines($entityTypeVarName, $model),
            [
                '            );',
                '',
                '            return $this->historyEventsFactory->createHistoryEvent(',
                sprintf('                %s::class,', $entityEditedHistoryEventClassName),
                '                $authenticatedUser,',
                '                $account,',
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
        TypeInterface $model,
        string $body = null,
    ): void
    {
        $processMethod = $appendTo
            ->addMethod('processRequest')
            ->setReturnType('ServiceResultInterface');

        $processMethod->addParameter('request')->setType('ServerRequestInterface');
        $processMethod->addParameter('formData')->setType('FormDataInterface');
        $processMethod->addParameter('account')->setType('AccountInterface');
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
            UserInterface::class,
            ServiceResultInterface::class,
            AccountInterface::class,
            ltrim($modelInterfaceFqn, '\\'),
        ];

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $modelServicesPath, $deleteEntityServiceInterface),
            $modelServicesNamespace,
            $interfaceUseStatements,
            $this->createDeleteEntityServiceInterface(
                $deleteEntityServiceInterface,
                $baseServiceInterfaceName,
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
        TypeInterface $model,
    ): string
    {
        $interface = new InterfaceType($interfaceName);
        $interface->addExtend($baseServiceInterface);

        $this->appendProcessRequestMethodForDeleteService($interface, $model);

        return (string) $interface;
    }

    private function createDeleteEntityServiceClass(
        string $className,
        string $interfaceName,
        string $baseServiceClassName,
        string $entityDeletedHistoryEventClassName,
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

        $processRequestBody = [
            'return $this->withinTransaction(',
            sprintf('    function () use ($account, $%s, $authenticatedUser) {', $entityVarName),
            sprintf('        $%s = $%s->getId();', $idVarName, $entityVarName),
            '',
            sprintf('        $%s->delete();', $entityVarName),
            '',
            '        return $this->historyEventsFactory->createHistoryEvent(',
            sprintf('            %s::class,', $entityDeletedHistoryEventClassName),
            '            $authenticatedUser,',
            '            $account,',
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
            $model,
            implode("\n", $processRequestBody),
        );

        return (string) $class;
    }

    private function appendProcessRequestMethodForDeleteService(
        ClassType|InterfaceType $appendTo,
        TypeInterface $model,
        string $body = null,
    ): void
    {
        $processMethod = $appendTo
            ->addMethod('processRequest')
            ->setReturnType('ServiceResultInterface');

        $processMethod->addParameter('request')->setType('ServerRequestInterface');
        $processMethod->addParameter('formData')->setType('FormDataInterface');
        $processMethod->addParameter('account')->setType('AccountInterface');
        $processMethod->addParameter(lcfirst($model->getEntityClassName()))->setType($model->getEntityInterfaceName());
        $processMethod->addParameter('authenticatedUser')->setType('UserInterface');

        if ($body) {
            $processMethod->setBody($body);
        }
    }
}
