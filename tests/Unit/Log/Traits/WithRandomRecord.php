<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Log\Traits;

use Psr\Log\LogLevel;
use Shirokovnv\PsrEx\Log\LogRecord;
use Shirokovnv\PsrEx\Tests\Traits\WithFaker;

trait WithRandomRecord
{
    use WithFaker;

    /**
     * @var array<string>
     */
    protected array $logLevels = [
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::DEBUG,
        LogLevel::EMERGENCY,
        LogLevel::ERROR,
        LogLevel::INFO,
        LogLevel::NOTICE,
        LogLevel::WARNING
    ];

    /**
     * Creates log record with random data.
     *
     * Returns [level, message, context, record] if $shouldReturnSeeds is true.
     *
     * Otherwise, returns LogRecord.
     *
     * @param bool $shouldReturnSeeds
     * @return array|LogRecord
     */
    private function createRandomRecord(bool $shouldReturnSeeds = true): array|LogRecord
    {
        $level = $this->faker->randomElement($this->logLevels);
        $message = $this->faker->sentence;
        $context = [ 'context' => $this->faker->sentence ];

        $record = new LogRecord($level, $message, $context);

        return $shouldReturnSeeds
            ? [$level, $message, $context, $record]
            : $record;
    }
}
