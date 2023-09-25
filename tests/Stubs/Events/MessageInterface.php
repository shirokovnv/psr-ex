<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Events;

interface MessageInterface
{
    /**
     * @return void
     */
    public function sayHello(): void;
}
