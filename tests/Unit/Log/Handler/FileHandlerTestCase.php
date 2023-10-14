<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Log\Handler;

use PHPUnit\Framework\TestCase;
use Shirokovnv\PsrEx\Log\Handler\FileHandler;
use Shirokovnv\PsrEx\Log\Handler\HandlerInterface;
use Shirokovnv\PsrEx\Tests\Unit\Log\Traits\WithVfs;

class FileHandlerTestCase extends TestCase
{
    use WithVfs;

    /**
     * @var HandlerInterface
     */
    private HandlerInterface $handler;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpVfs();
        $this->handler = new FileHandler($this->getVfsLogPath());
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->tearDownVfs();
        unset($this->handler);
    }

    /**
     * @return void
     */
    public function testWritingToFileIsCorrect(): void
    {
        $startMessage = 'start logging';
        $outputArr = [
            'log message 1',
            'log message 2',
            'log message 3'
        ];
        $endMessage = 'end logging';

        $this->assertFalse($this->logDir->hasChild($this->logFileName));
        $this->handler->handle($startMessage);
        $this->assertTrue($this->logDir->hasChild($this->logFileName));

        $summarize = $startMessage . PHP_EOL;
        foreach ($outputArr as $output) {
            $this->handler->handle($output);
            $summarize .= $output . PHP_EOL;
            $actualOutput = file_get_contents($this->getVfsLogPath());
            $this->assertEquals($summarize, $actualOutput);
        }

        $this->handler->handle($endMessage);
        $summarize .= $endMessage . PHP_EOL;
        $actualOutput = file_get_contents($this->getVfsLogPath());
        $this->assertEquals($summarize, $actualOutput);
    }
}
