<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Log;

class LogRecord
{
    /**
     * @var mixed
     */
    private mixed $level;

    /**
     * @var \Stringable|string
     */
    private \Stringable|string $message;

    /**
     * @var array<int|string, mixed>
     */
    private array $context;

    /**
     * @param mixed $level
     * @param \Stringable|string $message
     * @param array<int|string, mixed> $context
     */
    public function __construct($level, \Stringable|string $message, array $context = [])
    {
        $this->level = $level;
        $this->message = $message;
        $this->context = $context;
    }

    /**
     * @return mixed
     */
    public function getLevel(): mixed
    {
        return $this->level;
    }

    /**
     * @return string|\Stringable
     */
    public function getMessage(): \Stringable|string
    {
        return $this->message;
    }

    /**
     * @return array<int|string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
