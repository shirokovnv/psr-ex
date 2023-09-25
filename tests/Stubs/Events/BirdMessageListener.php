<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Events;

class BirdMessageListener
{
    /**
     * @param BirdMessageEvent $event
     * @return void
     */
    public function __invoke(BirdMessageEvent $event): void
    {
        $event->sayHello();
    }
}
