<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Events;

class CatMessageEvent extends AbstractMessageEvent
{
    /**
     * @return void
     */
    public function sayHello(): void
    {
        echo 'miu';
    }
}
