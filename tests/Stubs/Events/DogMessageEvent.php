<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Events;

class DogMessageEvent extends AbstractMessageEvent
{
    /**
     * @return void
     */
    public function sayHello(): void
    {
        echo 'woof';
    }
}
