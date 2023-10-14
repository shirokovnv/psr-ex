<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Log\Formatter;

use Shirokovnv\PsrEx\Log\LogRecord;

interface FormatterInterface
{
    /**
     * @param LogRecord $record
     * @return string
     */
    public function format(LogRecord $record): string;
}
