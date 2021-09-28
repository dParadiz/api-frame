<?php

namespace Api\Http\Router;

use InvalidArgumentException;

class RouteCollectionDiPersister
{

    public function __construct(
        private int $directoryPermissions = 0744,
        private int $filePermissions = 0644,
    )
    {
    }

    public function persist(RouteCollection $collection, string $fileName): void
    {
        ob_start();
        require __DIR__ . '/PHPDIDefinitionTemplate.php';
        $fileContent = ob_get_clean();

        $fileContent = "<?php declare(strict_types=1);\n" . $fileContent;

        $this->createCompilationDirectory(dirname($fileName));
        $this->writeFileAtomic($fileName, $fileContent);
    }

    private function createCompilationDirectory(string $directory): void
    {
        if (!is_dir($directory) && !@mkdir($directory, $this->directoryPermissions, true) && !is_dir($directory)) {
            throw new InvalidArgumentException(sprintf('Compilation directory does not exist and cannot be created: %s.',
                $directory));
        }
        if (!is_writable($directory)) {
            throw new InvalidArgumentException(sprintf('Compilation directory is not writable: %s.', $directory));
        }
    }

    private function writeFileAtomic(string $fileName, string $content): void
    {
        $tmpFile = @tempnam(dirname($fileName), 'swap-config');
        if ($tmpFile === false) {
            throw new InvalidArgumentException(
                sprintf('Error while creating temporary file in %s', dirname($fileName))
            );
        }
        @chmod($tmpFile, $this->filePermissions);

        $written = file_put_contents($tmpFile, $content);
        if ($written === false) {
            @unlink($tmpFile);

            throw new InvalidArgumentException(sprintf('Error while writing to %s', $tmpFile));
        }

        @chmod($tmpFile, $this->filePermissions);
        $renamed = @rename($tmpFile, $fileName);
        if (!$renamed) {
            @unlink($tmpFile);
            throw new InvalidArgumentException(sprintf('Error while renaming %s to %s', $tmpFile, $fileName));
        }
    }
}