<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Log\Handler;

interface HandlerInterface
{
    /**
     * @param string $output
     * @return void
     */
    public function handle(string $output): void;
}
