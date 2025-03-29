<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro;

use ActiveCollab\DatabaseStructure\TypeInterface;
use ActiveCollab\Retro\Bootstrapper\Bundle\BundleInterface;
use ActiveCollab\Retro\CommandTrait\BundleAwareTrait;
use ActiveCollab\Retro\CommandTrait\FileManagementTrait;
use ActiveCollab\Retro\CommandTrait\ModelAwareTrait;
use ActiveCollab\Retro\CommandTrait\ServiceContextAwareTrait;
use ActiveCollab\Retro\Integrate\Creator\CreatorInterface;
use ActiveCollab\Retro\Service\Result\InvalidFormData\InvalidFormData;
use ActiveCollab\Retro\Sitemap\Controller\CrudController;
use ActiveCollab\Retro\Sitemap\Trait\EntityAwareNodeMiddleware;
use Doctrine\Inflector\Inflector;
use Nette\PhpGenerator\ClassType;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class CreateCrudController extends RetroCommand
{
    use FileManagementTrait;
    use ModelAwareTrait;
    use BundleAwareTrait;
    use ServiceContextAwareTrait;

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Create CRUD controllers for the model.')
            ->addArgument(
                'model',
                InputArgument::REQUIRED,
                'Name of the model that CRUD controllers should be created for.',
            )
            ->addArgument(
                'bundle',
                InputArgument::OPTIONAL,
                'Name of the bundle where controllers should be created at.',
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

            $output->writeln(
                sprintf(
                    'Building CRUD controllers for <comment>%s</comment> model in <comment>%s</comment> bundle...',
                    $model->getName(),
                    $bundle->getName(),
                ),
            );

            $controllersPath = sprintf('%s/Controller', $bundle::PATH);
            $this->mustCreateDir($controllersPath, $output);

            $controllersNamespace = $this->getControllerNamespace($bundle);
            $modelInterfaceFqn = $this->getModelFqn(
                sprintf(
                    '%sInterface',
                    $model->getEntityClassName(),
                ),
            );
            $modelFqn = $this->getModelFqn($model->getEntityClassName());

            $modelServicesNamespace = sprintf(
                '%s\\Service\\%s',
                $this->get(CreatorInterface::class)->getBundleNamespace($bundle),
                $model->getEntityClassName(),
            );

            $formComponentClassName = sprintf('%sFormTag', $model->getEntityClassName());
            $formComponentFqn = sprintf(
                '%s\\TemplateExtension\\%s\\%s',
                $this->get(CreatorInterface::class)->getBundleNamespace($bundle),
                sprintf('%sForm', $model->getEntityClassName()),
                $formComponentClassName,
            );

            $this->createEntitiesController(
                $controllersPath,
                $controllersNamespace,
                $modelServicesNamespace,
                $formComponentClassName,
                $formComponentFqn,
                $model,
                $output,
            );

            $this->createEntityController(
                $controllersPath,
                $controllersNamespace,
                $modelFqn,
                $modelInterfaceFqn,
                $modelServicesNamespace,
                $formComponentClassName,
                $formComponentFqn,
                $model,
                $output,
            );

            return 0;
        } catch (Throwable $e) {
            return $this->abortDueToException($e, $input, $output);
        }
    }

    private function createEntitiesController(
        string $controllersPath,
        string $controllersNamespace,
        string $servicesNamespace,
        string $formComponentClassName,
        string $formComponentFqn,
        TypeInterface $model,
        OutputInterface $output,
    ): void
    {
        $entitiesControllerClassName = sprintf('%sController', $model->getManagerClassName());

        $addEntityServiceInterface = sprintf('Add%sServiceInterface', $model->getEntityClassName());
        $entityAddedHistoryEventInterface = sprintf('%sAddedHistoryEventInterface', $model->getEntityClassName());

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $controllersPath, $entitiesControllerClassName),
            $controllersNamespace,
            [
                CrudController::class,
                AccountAwareMiddlewareTrait::class,
                AuthenticationAwareMiddlewareTrait::class,
                EntityAwareNodeMiddleware::class,
                ServerRequestInterface::class,
                ResponseInterface::class,
                sprintf('%s\\%s', $servicesNamespace, $addEntityServiceInterface),
                sprintf('%s\\Result\\%s', $servicesNamespace, $entityAddedHistoryEventInterface),
                $formComponentFqn,
            ],
            $this->createEntitiesControllerClass(
                $entitiesControllerClassName,
                $addEntityServiceInterface,
                $entityAddedHistoryEventInterface,
                $formComponentClassName,
                $model,
            ),
            $output,
        );
    }

    private function createEntitiesControllerClass(
        string $className,
        string $addEntityServiceInterface,
        string $entityAddedHistoryEventInterface,
        string $formComponentClassName,
        TypeInterface $model,
    ): string
    {
        $class = new ClassType($className);
        $class->setExtends('CrudController');
        $class->addTrait('AccountAwareMiddlewareTrait');
        $class->addTrait('AuthenticationAwareMiddlewareTrait');

        $this->addAction($class, 'indexAction', 'return $this->proceedToNode($request);');
        $this->addAction(
            $class,
            'addAction',
            implode(
                "\n",
                [
                    '$account = $this->mustGetAccount($request);',
                    '',
                    '$formData = $this->getFormDataFactory()->createFormData();',
                    '',
                    '$request = $request->withAttribute(\'formData\', $formData);',
                    '',
                    'if ($this->isPost($request)) {',
                    sprintf('    $result = $this->get(%s::class)->processRequest(', $addEntityServiceInterface),
                    '        $request,',
                    '        $formData,',
                    '        $account,',
                    '        $this->getAuthenticatedUser($request),',
                    '    );',
                    '',
                    sprintf('    if ($result instanceof %s) {', $entityAddedHistoryEventInterface),
                    '        # @TODO: Consider providing a better return URL after entity is added.',
                    '        return $this->redirect($request, $result->extendWithSignature($account->getUrl()));',
                    '    }',
                    '',
                    '    # @TODO: Fix add form URL, as well as return URL.',
                    '    return $this->renderContent(',
                    sprintf('        $this->getExtension(%s::class)->render(', $formComponentClassName),
                    '            $result,',
                    '            $formData,',
                    '            $account,',
                    '            $account->getUrl(),',
                    sprintf('            "Add %s",', $this->getVerboseEntityName($model->getEntityClassName())),
                    '            $account->getUrl(),',
                    sprintf('            "#%s",', $this->getFormName('add', $model->getEntityClassName())),
                    '        ),',
                    '        429,',
                    '    );',
                    '}',
                    'return $this->proceedToNode($request);',
                ],
            ),
        );

        return (string) $class;
    }

    private function createEntityController(
        string $controllersPath,
        string $controllersNamespace,
        string $modelFqn,
        string $modelInterfaceFqn,
        string $servicesNamespace,
        string $formComponentClassName,
        string $formComponentFqn,
        TypeInterface $model,
        OutputInterface $output,
    ): void
    {
        $entityControllerClassName = sprintf('%sController', $model->getEntityClassName());

        $editEntityServiceInterface = sprintf('Edit%sServiceInterface', $model->getEntityClassName());
        $entityEditedHistoryEventInterface = sprintf('%sEditedHistoryEventInterface', $model->getEntityClassName());
        $deleteEntityServiceInterface = sprintf('Delete%sServiceInterface', $model->getEntityClassName());
        $entityDeletedHistoryEventInterface = sprintf('%sDeletedHistoryEventInterface', $model->getEntityClassName());

        $trimmedServiceNamespace = ltrim($servicesNamespace, '\\');

        $this->mustCreatePhpFile(
            sprintf('%s/%s.php', $controllersPath, $entityControllerClassName),
            $controllersNamespace,
            [
                CrudController::class,
                AccountAwareMiddlewareTrait::class,
                AuthenticationAwareMiddlewareTrait::class,
                ServerRequestInterface::class,
                ResponseInterface::class,
                InvalidFormData::class,
                ltrim($modelInterfaceFqn, '\\'),
                ltrim($modelFqn, '\\'),
                sprintf('%s\\%s', $trimmedServiceNamespace, $editEntityServiceInterface),
                sprintf('%s\\Result\\%s', $trimmedServiceNamespace, $entityEditedHistoryEventInterface),
                sprintf('%s\\%s', $trimmedServiceNamespace, $deleteEntityServiceInterface),
                sprintf('%s\\Result\\%s', $trimmedServiceNamespace, $entityDeletedHistoryEventInterface),
                ltrim($formComponentFqn, '\\'),
            ],
            $this->createEntityControllerClass(
                $entityControllerClassName,
                $model,
                $editEntityServiceInterface,
                $entityEditedHistoryEventInterface,
                $deleteEntityServiceInterface,
                $entityDeletedHistoryEventInterface,
                $formComponentClassName,
            ),
            $output,
        );
    }

    private function createEntityControllerClass(
        string $className,
        TypeInterface $model,
        string $editEntityServiceInterface,
        string $entityEditedHistoryEventInterface,
        string $deleteEntityServiceInterface,
        string $entityDeletedHistoryEventInterface,
        string $formComponentClassName,
    ): string
    {
        $class = new ClassType($className);
        $class->setExtends('CrudController');
        $class->addTrait('AccountAwareMiddlewareTrait');
        $class->addTrait('AuthenticationAwareMiddlewareTrait');
        $class->addTrait('EntityAwareNodeMiddleware');

        $entityInstancePropertyName = lcfirst($model->getEntityClassName());

        $class
            ->addProperty($entityInstancePropertyName, null)
            ->setPrivate()
            ->setType(sprintf('?%s', $model->getEntityInterfaceName()));

        $beforeMethod = $class
            ->addMethod('before')
            ->setProtected()
            ->setReturnType('ServerRequestInterface|ResponseInterface')
            ->setBody(
                implode(
                    "\n",
                    [
                        sprintf('$this->%s = $this->getEntityFromRequest($request, %s::class);', $entityInstancePropertyName, $model->getEntityClassName()),
                        '',
                        sprintf('if (!$this->%s instanceof %s) {', $entityInstancePropertyName, $model->getEntityInterfaceName()),
                        '    return $this->notFound();',
                        '}',
                        '',
                        sprintf('return $request->withAttribute(%s, $this->%s);', var_export(sprintf('active%s', $model->getEntityClassName()), true), $entityInstancePropertyName),
                    ],
                ),
            );
        $beforeMethod->addParameter('request')->setType('ServerRequestInterface');

        $this->addAction($class, 'indexAction', 'return $this->proceedToNode($request);');
        $this->addAction(
            $class,
            'editAction',
            implode(
                "\n",
                [
                    '$account = $this->mustGetAccount($request);',
                    '',
                    '$formData = $this->getFormDataFactory()->createFormData(',
                    '    [',
                    '        # @TODO: Add form fields here.',
                    '    ],',
                    ');',
                    '',
                    '$request = $request->withAttribute(\'formData\', $formData);',
                    '',
                    'if ($this->isPost($request)) {',
                    sprintf('    $result = $this->get(%s::class)->processRequest(', $editEntityServiceInterface),
                    '        $request,',
                    '        $formData,',
                    '        $account,',
                    sprintf('        $this->%s,', $entityInstancePropertyName),
                    '        $this->getAuthenticatedUser($request),',
                    '    );',
                    '',
                    sprintf('    if ($result instanceof %s) {', $entityEditedHistoryEventInterface),
                    sprintf('        return $this->redirect($request, $result->extendWithSignature($this->%s->getUrl()));', $entityInstancePropertyName),
                    '    }',
                    '',
                    '    return $this->renderContent(',
                    sprintf('        $this->getExtension(%s::class)->render(', $formComponentClassName),
                    '            $result,',
                    '            $formData,',
                    '            $account,',
                    sprintf('            $this->%s->getUrl("edit"),', $entityInstancePropertyName),
                    sprintf('            "Edit %s",', $this->getVerboseEntityName($model->getEntityClassName())),
                    sprintf('            $this->%s->getUrl(),', $entityInstancePropertyName),
                    sprintf('            "#%s",', $this->getFormName('edit', $model->getEntityClassName())),
                    '        ),',
                    '        429,',
                    '    );',
                    '}',
                    '',
                    'return $this->proceedToNode($request);',
                ],
            ),
        );
        $this->addAction($class,
            'deleteAction',
            implode(
                "\n",
                [
                    'if ($this->isPost($request)) {',
                    '    $account = $this->getAccount($request);',
                    '',
                    sprintf('    $result = $this->get(%s::class)->processRequest(', $deleteEntityServiceInterface),
                    '        $request,',
                    '        $this->getFormDataFactory()->createFormData(),',
                    '        $account,',
                    sprintf('        $this->%s,', $entityInstancePropertyName),
                    '        $this->getAuthenticatedUser($request),',
                    '    );',
                    '',
                    sprintf('    if ($result instanceof %s) {', $entityDeletedHistoryEventInterface),
                    '        # @TODO: Consider providing a better return URL after delete action.',
                    '        return $this->redirect($request, $result->extendWithSignature($account->getUrl()));',
                    '    } elseif ($result instanceof InvalidFormData) {',
                    '        $request = $request->withAttribute("formData", $result->getFormData());',
                    '    }',
                    '}',
                    '',
                    'return $this->proceedToNode($request);',
                ],
            ),
        );

        return (string) $class;
    }

    private function addAction(ClassType $class, string $actionName, string $body): void
    {
        $actionMethod = $class
            ->addMethod($actionName)
            ->setProtected()
            ->setReturnType('ResponseInterface')
            ->setBody($body);
        $actionMethod->addParameter('request')->setType('ServerRequestInterface');
    }

    private function getVerboseEntityName(string $entityClassName): string
    {
        return trim(preg_replace('/[A-Z]/', ' $0', $entityClassName));
    }

    private function getFormName(string $action, string $entityClassName): string
    {
        return sprintf(
            '%s_%s_form',
            $action,
            $this->getContainer()->get(Inflector::class)->tableize($entityClassName),
        );
    }

    private function getControllerNamespace(BundleInterface $bundle): string
    {
        return sprintf(
            '\\' . ltrim('%s\\Controller', '\\'),
            $this->get(CreatorInterface::class)->getBundleNamespace($bundle),
        );
    }

    private function getModelFqn(string $element): string
    {
        return sprintf(
            '\\' . ltrim('%s\\%s', '\\'),
            $this->get(CreatorInterface::class)->getModelNamespace(),
            $element,
        );
    }
}
