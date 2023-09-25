<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Events;

class CatMessageListener
{
    /**
     * @param CatMessageEvent $event
     * @return void
     */
    public function __invoke(CatMessageEvent $event): void
    {
        $event->sayHello();
    }
}
