<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Log;

use Psr\Log\AbstractLogger;
use Shirokovnv\PsrEx\Log\Formatter\FormatterInterface;
use Shirokovnv\PsrEx\Log\Handler\HandlerInterface;

class Logger extends AbstractLogger
{
    /**
     * @var HandlerInterface
     */
    private HandlerInterface $handler;

    /**
     * @var FormatterInterface
     */
    private FormatterInterface $formatter;

    /**
     * @param HandlerInterface $handler
     * @param FormatterInterface $formatter
     */
    public function __construct(
        HandlerInterface $handler,
        FormatterInterface $formatter
    ) {
        $this->handler = $handler;
        $this->formatter = $formatter;
    }

    /**
     * @param $level
     * @param \Stringable|string $message
     * @param array<int|string, mixed> $context
     * @return void
     */
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $dataToBeLogged = $this->formatter->format(new LogRecord($level, $message, $context));
        $this->handler->handle($dataToBeLogged);
    }
}
