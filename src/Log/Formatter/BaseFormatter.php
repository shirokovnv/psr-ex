<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Log\Formatter;

use Shirokovnv\PsrEx\Log\LogRecord;

class BaseFormatter implements FormatterInterface
{
    protected const KEY_MESSAGE = 'message';

    protected const KEY_LEVEL = 'level';

    protected const KEY_TIMESTAMP = 'timestamp';

    protected const DEFAULT_DATETIME_FORMAT = 'c';

    protected const DEFAULT_FORMAT = '%timestamp% [%level%]: %message%';

    protected const DEFAULT_SEPARATOR = ',';

    /**
     * @param LogRecord $record
     * @return string
     */
    public function format(LogRecord $record): string
    {
        $outputArr = [
            self::KEY_MESSAGE => $this->interpolate($record->getMessage(), $record->getContext()),
            self::KEY_LEVEL => $this->stringifyLevel($record->getLevel()),
            self::KEY_TIMESTAMP => (new \DateTimeImmutable())->format(self::DEFAULT_DATETIME_FORMAT),
        ];

        $outputStr = self::DEFAULT_FORMAT;
        foreach($outputArr as $key => $value) {
            $outputStr = str_replace('%' . $key . '%', $value, $outputStr);
        }
        return $outputStr;
    }

    /**
     * @param \Stringable|string $message
     * @param array<int|string, mixed> $context
     *
     * @return string
     */
    protected function interpolate(\Stringable|string $message, array $context = []): string
    {
        $replace = [];
        foreach($context as $key => $value) {
            if (is_string($value) || $value instanceof \Stringable) {
                $replace['{' . $key . '}'] = $value;
            }
        }
        return strtr((string) $message, $replace);
    }

    /**
     * @param mixed $level
     * @return string
     */
    protected function stringifyLevel(mixed $level): string
    {
        if (is_string($level) || $level instanceof \Stringable) {
            return strtoupper((string) $level);
        }

        if (is_array($level)) {
            return implode(self::DEFAULT_SEPARATOR, $level);
        }

        if (is_null($level) || is_int($level) || is_float($level) || is_bool($level)) {
            return (string) $level;
        }

        return serialize($level);
    }
}
