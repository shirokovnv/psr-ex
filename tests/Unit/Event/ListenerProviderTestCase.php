<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Event;

use Shirokovnv\PsrEx\Tests\Stubs\Events\AbstractMessageEvent;
use Shirokovnv\PsrEx\Tests\Stubs\Events\MessageInterface;
use Shirokovnv\PsrEx\Tests\Stubs\Events\CatMessageListener;
use Shirokovnv\PsrEx\Tests\Stubs\Events\CatMessageEvent;
use Shirokovnv\PsrEx\Tests\Stubs\Events\DogMessageListener;
use Shirokovnv\PsrEx\Tests\Stubs\Events\DogMessageEvent;

class ListenerProviderTestCase extends EventTestCase
{
    /**
     * @return void
     */
    public function testConcreteBindings(): void
    {
        // Test concrete bindings
        $mapEventToListener = [
            CatMessageEvent::class => new CatMessageListener(),
            DogMessageEvent::class => new DogMessageListener(),
        ];

        foreach($mapEventToListener as $eventClass => $listener) {
            $this->listenerProvider->addListener($eventClass, $listener);

            $event = new $eventClass();

            $listeners = $this->listenerProvider->getListenersForEvent($event);
            $this->assertCount(1, $listeners);
            $this->assertInstanceOf($listener::class, $listeners[0]);
        }
    }

    /**
     * @return void
     */
    public function testAbstractBindings(): void
    {
        $listeners = [new CatMessageListener(), new DogMessageListener()];
        $mapEventToListener = [
            AbstractMessageEvent::class => $listeners,
            MessageInterface::class => $listeners
        ];

        foreach($mapEventToListener as $abstract => $listenerArr) {
            foreach($listenerArr as $listener) {
                $this->listenerProvider->addListener($abstract, $listener);
            }
        }

        $events = [new CatMessageEvent(), new DogMessageEvent()];
        foreach($events as $event) {
            $actualListeners = $this->listenerProvider->getListenersForEvent($event);
            $this->assertCount(2, $actualListeners);
            $this->assertEquals($listeners, $actualListeners);
        }
    }

    /**
     * @return void
     */
    public function testEmptyListeners(): void
    {
        $events = [new CatMessageEvent(), new DogMessageEvent()];
        $randomEvent = $events[array_rand($events)];

        $listeners = $this->listenerProvider->getListenersForEvent($randomEvent);
        $this->assertCount(0, $listeners);
    }

    /**
     * @return void
     */
    public function testNotFoundListeners(): void
    {
        $this->listenerProvider->addListener(CatMessageEvent::class, new CatMessageListener());
        $listeners = $this->listenerProvider->getListenersForEvent(new DogMessageEvent());
        $this->assertCount(0, $listeners);
    }

    /**
     * @return void
     */
    public function testClearListeners(): void
    {
        // Try to add some events, then remove listener by class name
        $this->listenerProvider->addListener(CatMessageEvent::class, new CatMessageListener());
        $listeners = $this->listenerProvider->getListenersForEvent(new CatMessageEvent());
        $this->assertCount(1, $listeners);
        $this->assertInstanceOf(CatMessageListener::class, $listeners[0]);

        $this->listenerProvider->clearListeners(CatMessageEvent::class);
        $listeners = $this->listenerProvider->getListenersForEvent(new CatMessageEvent());
        $this->assertCount(0, $listeners);

        // Repeat adding, then remove all listeners
        $this->listenerProvider->addListener(DogMessageEvent::class, new DogMessageListener());
        $listeners = $this->listenerProvider->getListenersForEvent(new DogMessageEvent());
        $this->assertCount(1, $listeners);
        $this->assertInstanceOf(DogMessageListener::class, $listeners[0]);

        $this->listenerProvider->clearListeners();
        $listeners = $this->listenerProvider->getListenersForEvent(new DogMessageEvent());
        $this->assertCount(0, $listeners);
    }
}
