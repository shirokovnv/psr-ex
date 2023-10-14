<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Log;

use PHPUnit\Framework\TestCase;
use Shirokovnv\PsrEx\Log\LogRecord;
use Shirokovnv\PsrEx\Tests\Unit\Log\Traits\WithRandomRecord;

class LogRecordTestCase extends TestCase
{
    use WithRandomRecord;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->tearDownFaker();
    }

    /**
     * @return void
     */
    public function testInitializationAndGetters(): void
    {
        /** @var LogRecord $record */
        [$level, $message, $context, $record] = $this->createRandomRecord();

        $this->assertInstanceOf(LogRecord::class, $record);
        $this->assertEquals($level, $record->getLevel());
        $this->assertEquals($message, $record->getMessage());
        $this->assertEquals($context, $record->getContext());
    }
}
