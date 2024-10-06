<?php

/*
 * This file is part of the ActiveCollab Retro project.
 *
 * (c) A51 doo <info@activecollab.com>
 */

declare(strict_types=1);

namespace ActiveCollab\Retro\CommandTrait;

use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

trait FileManagementTrait
{
    protected function mustCreateDir(
        string $path,
        OutputInterface $output,
        string $pathRelativeTo = null
    ): void
    {
        if (is_dir($path)) {
            $output->writeln(
                sprintf(
                    'Directory <comment>%s</comment> already exists.',
                    $pathRelativeTo
                        ? $this->getPathRelativeTo($path, $pathRelativeTo)
                        : $this->getPathRelativeToSrc($path),
                )
            );

            return;
        }

        $oldMask = umask(0);
        $dirCreated = mkdir($path);
        umask($oldMask);

        if (!$dirCreated) {
            throw new RuntimeException(
                sprintf(
                    'Failed to create directory "%s".',
                    $pathRelativeTo
                        ? $this->getPathRelativeTo($path, $pathRelativeTo)
                        : $this->getPathRelativeToSrc($path),
                )
            );
        }

        $output->writeln(
            sprintf(
                'Directory <comment>%s</comment> created.',
                $pathRelativeTo
                    ? $this->getPathRelativeTo($path, $pathRelativeTo)
                    : $this->getPathRelativeToSrc($path),
            )
        );
    }

    protected function mustCreateFile(
        string $filePath,
        string $content,
        OutputInterface $output,
        string $pathRelativeTo = null,
    ): void
    {
        if (is_file($filePath)) {
            $output->writeln(
                sprintf(
                    'File <comment>%s</comment> already exists. Skipping...',
                    $pathRelativeTo
                        ? $this->getPathRelativeTo($filePath, $pathRelativeTo)
                        : $this->getPathRelativeToSrc($filePath),
                )
            );

            return;
        }

        $fileWritten = file_put_contents($filePath, $content);

        if (!$fileWritten) {
            throw new RuntimeException(
                sprintf(
                    'Failed to write content to "%s".',
                    $pathRelativeTo
                        ? $this->getPathRelativeTo($filePath, $pathRelativeTo)
                        : $this->getPathRelativeToSrc($filePath),
                )
            );
        }

        $output->writeln(
            sprintf(
                'Created <comment>%s</comment>.',
                $pathRelativeTo
                    ? $this->getPathRelativeTo($filePath, $pathRelativeTo)
                    : $this->getPathRelativeToSrc($filePath),
            )
        );
    }

    protected function mustCreatePhpFile(
        string $filePath,
        string $namespace,
        array $useStatements,
        string $content,
        OutputInterface $output,
        string $pathRelativeTo = null,
    ): void
    {
        if (is_file($filePath)) {
            $output->writeln(
                sprintf(
                    'File <comment>%s</comment> already exists. Skipping...',
                    $pathRelativeTo
                        ? $this->getPathRelativeTo($filePath, $pathRelativeTo)
                        : $this->getPathRelativeToSrc($filePath),
                )
            );

            return;
        }

        $useString = '';

        if (!empty($useStatements)) {
            sort($useStatements);

            foreach ($useStatements as $useStatement) {
                $useString .= sprintf("use %s;\n", $useStatement);
            }

            $useString .= "\n";
        }

        $fileWritten = file_put_contents(
            $filePath,
            sprintf(
                "<?php\n\ndeclare(strict_types=1);\n\nnamespace %s;\n\n%s%s",
                ltrim($namespace, '\\'),
                $useString,
                $content,
            )
        );

        if (!$fileWritten) {
            throw new RuntimeException(
                sprintf(
                    'Failed to write content to "%s".',
                    $pathRelativeTo
                        ? $this->getPathRelativeTo($filePath, $pathRelativeTo)
                        : $this->getPathRelativeToSrc($filePath),
                )
            );
        }

        $output->writeln(
            sprintf(
                'Created <comment>%s</comment>.',
                $pathRelativeTo
                    ? $this->getPathRelativeTo($filePath, $pathRelativeTo)
                    : $this->getPathRelativeToSrc($filePath),
            )
        );
    }

    protected function getPathRelativeToSrc(string $path): string
    {
        return $this->getPathRelativeTo($path, 'app/current/src');
    }

    private array $absolutePaths = [];

    protected function getPathRelativeTo(string $path, string $relativeToPath): string
    {
        if (empty($this->absolutePaths[$relativeToPath])) {
            $this->absolutePaths[$relativeToPath] = sprintf(
                '%s/%s',
                $this->getAppPath(),
                $relativeToPath,
            );
        }

        return substr($path, strlen($this->absolutePaths[$relativeToPath]) + 1);
    }

    abstract protected function getAppPath(): string;
}
