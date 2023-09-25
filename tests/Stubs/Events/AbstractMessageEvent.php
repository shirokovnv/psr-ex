<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Events;

abstract class AbstractMessageEvent implements MessageInterface
{
    /**
     * @return void
     */
    abstract public function sayHello(): void;
}
