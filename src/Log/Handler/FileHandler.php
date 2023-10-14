<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Log\Handler;

class FileHandler implements HandlerInterface
{
    /**
     * @var string
     */
    private string $filename;

    /**
     * @var bool
     */
    private bool $isInitialized = false;

    /**
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * @param string $output
     * @return void
     */
    public function handle(string $output): void
    {
        $this->ensureInitialized();

        file_put_contents($this->filename, $output . PHP_EOL, FILE_APPEND);
    }

    /**
     * @return void
     */
    protected function ensureInitialized(): void
    {
        if (! $this->isInitialized) {
            $parentDir = dirname($this->filename);
            if (! file_exists($parentDir)) {
                $makeDirStatus = mkdir($parentDir, 0777, true);
                if ($makeDirStatus === false && !is_dir($parentDir)) {
                    throw new \UnexpectedValueException(
                        sprintf('Error opening directory: %s', $parentDir)
                    );
                }
            }
            $this->isInitialized = true;
        }
    }
}
