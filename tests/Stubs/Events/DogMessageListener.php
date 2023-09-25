<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Events;

class DogMessageListener
{
    /**
     * @param DogMessageEvent $event
     * @return void
     */
    public function __invoke(DogMessageEvent $event): void
    {
        $event->sayHello();
    }
}
