<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Log\Formatter;

use PHPUnit\Framework\TestCase;
use Shirokovnv\PsrEx\Log\Formatter\BaseFormatter;
use Shirokovnv\PsrEx\Log\Formatter\FormatterInterface;
use Shirokovnv\PsrEx\Tests\Unit\Log\Traits\WithRandomRecord;

class BaseFormatterTestCase extends TestCase
{
    use WithRandomRecord;

    /**
     * @var FormatterInterface
     */
    private FormatterInterface $formatter;

    /**
     * @var string
     */
    private string $timestampRegex =
        '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T(2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]/';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
        $this->formatter = new BaseFormatter();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->tearDownFaker();
        unset($this->formatter);
    }

    /**
     * @return void
     */
    public function testOutputFormatHasMainData(): void
    {
        [$level, $message, $context, $record] = $this->createRandomRecord();
        $output = $this->formatter->format($record);

        $containsDate = preg_match($this->timestampRegex, $output);
        $this->assertNotFalse($containsDate);
        $this->assertEquals(1, $containsDate);

        $this->assertStringContainsStringIgnoringCase($level, $output);
        $this->assertStringContainsStringIgnoringCase($message, $output);
    }
}
