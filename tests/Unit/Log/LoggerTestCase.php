<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Log;

use PHPUnit\Framework\TestCase;
use Shirokovnv\PsrEx\Log\Formatter\BaseFormatter;
use Shirokovnv\PsrEx\Log\Handler\FileHandler;
use Shirokovnv\PsrEx\Log\Logger;
use Shirokovnv\PsrEx\Tests\Unit\Log\Traits\WithRandomRecord;
use Shirokovnv\PsrEx\Tests\Unit\Log\Traits\WithVfs;

class LoggerTestCase extends TestCase
{
    use WithRandomRecord;
    use WithVfs;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
        $this->setUpVfs();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->tearDownFaker();
        $this->tearDownVfs();
    }

    /**
     * @return void
     */
    public function testFileLogger(): void
    {
        $logger = new Logger(new FileHandler($this->getVfsLogPath()), new BaseFormatter());
        [$level, $message, $context, $record] = $this->createRandomRecord();

        $this->assertFalse($this->logDir->hasChild($this->logFileName));
        $logger->log($level, $message, $context);
        $this->assertTrue($this->logDir->hasChild($this->logFileName));

        $logContent = file_get_contents($this->getVfsLogPath());

        $this->assertNotEmpty($logContent);
        $this->assertStringEndsWith(PHP_EOL, $logContent);
    }
}
