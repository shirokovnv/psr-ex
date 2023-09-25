<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Event;

use Shirokovnv\PsrEx\Tests\Stubs\Events\BirdMessageEvent;
use Shirokovnv\PsrEx\Tests\Stubs\Events\BirdMessageListener;
use Shirokovnv\PsrEx\Tests\Stubs\Events\CatMessageEvent;
use Shirokovnv\PsrEx\Tests\Stubs\Events\CatMessageListener;
use Shirokovnv\PsrEx\Tests\Stubs\Events\DogMessageEvent;
use Shirokovnv\PsrEx\Tests\Stubs\Events\DogMessageListener;

class EventDispatcherTestCase extends EventTestCase
{
    /**
     * @return void
     */
    public function testEventDispatching(): void
    {
        $this->expectOutputString('miuwoof');
        $this->listenerProvider->addListener(CatMessageEvent::class, new CatMessageListener());
        $this->eventDispatcher->dispatch(new CatMessageEvent());

        $this->listenerProvider->addListener(DogMessageEvent::class, new DogMessageListener());
        $this->eventDispatcher->dispatch(new DogMessageEvent());
    }

    /**
     * @return void
     */
    public function testStoppableEventDispatching(): void
    {
        $this->expectOutputString('');
        $this->listenerProvider->addListener(BirdMessageEvent::class, new BirdMessageListener());
        $this->eventDispatcher->dispatch(new BirdMessageEvent());
    }
}
