<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Log\Traits;

use bovigo\vfs\vfsStream;
use bovigo\vfs\vfsStreamDirectory;

trait WithVfs
{
    /**
     * @var  vfsStreamDirectory
     */
    protected vfsStreamDirectory $logDir;

    /**
     * @var string
     */
    protected string $logFileName;

    /**
     * @return void
     */
    public function setUpVfs(): void
    {
        $this->logDir = vfsStream::setup('exampleDir');
        $this->logFileName = 'log.txt';
    }

    /**
     * @return void
     */
    public function tearDownVfs(): void
    {
        if (isset($this->logDir)) {
            unset($this->logDir);
        }
        if (isset($this->logFileName)) {
            unset($this->logFileName);
        }
    }

    /**
     * @return string
     */
    public function getVfsLogPath(): string
    {
        return vfsStream::url('exampleDir/' . $this->logFileName);
    }
}
