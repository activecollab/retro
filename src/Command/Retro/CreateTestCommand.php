<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\Command\Retro;

use ActiveCollab\Retro\CommandTrait\FileManagementTrait;
use InvalidArgumentException;
use Nette\PhpGenerator\ClassType;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class CreateTestCommand extends RetroCommand
{
    use FileManagementTrait;

    public const BASE_TEST_TYPE = 'base';
    public const CONTAINER_TEST_TYPE = 'container';
    public const MODEL_TEST_TYPE = 'model';

    public const TEST_TYPES = [
        self::BASE_TEST_TYPE,
        self::CONTAINER_TEST_TYPE,
        self::MODEL_TEST_TYPE,
    ];

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Create a test class')
            ->addArgument('name', InputArgument::REQUIRED, 'Test name')
            ->addOption(
                'type',
                't',
                InputArgument::OPTIONAL,
                sprintf('Test type (%s)', implode(', ', self::TEST_TYPES)),
                self::BASE_TEST_TYPE,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $testClassName = $this->getTestClassName($input);

            [
                $baseTestTypeFqn,
                $baseTestTypeClassName,
            ] = $this->getBaseTestType($input);

            $this->mustCreatePhpFile(
                sprintf('%s/test/unit/src/%s.php', $this->getAppPath(), $testClassName),
                sprintf('%s\\Test\\Unit', $this->getAppNamespace()),
                [
                    $baseTestTypeFqn,
                ],
                $this->createTestClass(
                    $testClassName,
                    $baseTestTypeClassName,
                ),
                $output,
                'test/unit/src',
            );

            return 1;
        } catch (Throwable $e) {
            return $this->abortDueToException($e, $input, $output);
        }
    }

    private function createTestClass(
        string $testClassName,
        string $testExtends,
    ): string
    {
        $class = new ClassType($testClassName);
        $class->setExtends($testExtends);

        return (string) $class;
    }

    private function getTestClassName(InputInterface $input): string
    {
        return sprintf(
            '%sTest',
            $this->getInflector()->classify(
                str_replace(' ', '_', trim($input->getArgument('name'))),
            ),
        );
    }

    private function getBaseTestType(InputInterface $input): array
    {
        $testType = $input->getOption('type');

        if (!in_array($testType, self::TEST_TYPES)) {
            throw new InvalidArgumentException(sprintf('Invalid test type "%s"', $testType));
        }

        return match ($testType) {
            self::CONTAINER_TEST_TYPE => [
                sprintf('%s\\Test\\Unit\\Base\\ContainerTestCase', $this->getAppNamespace()),
                'ContainerTestCase',
            ],
            self::MODEL_TEST_TYPE => [
                sprintf('%s\\Test\\Unit\\Base\\ModelTestCase', $this->getAppNamespace()),
                'ModelTestCase',
            ],
            default => [
                sprintf('%s\\Test\\Unit\\Base\\TestCase', $this->getAppNamespace()),
                'TestCase',
            ],
        };
    }
}
