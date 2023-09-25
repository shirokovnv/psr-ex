<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var array<string, callable[]>
     */
    private array $listeners;

    /**
     * @param array<string, callable[]> $listeners
     */
    public function __construct(array $listeners = [])
    {
        $this->listeners = $listeners;
    }

    /**
     * @param object $event
     * @return iterable<callable>
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventClass = $event::class;

        return array_key_exists($eventClass, $this->listeners)
            ? $this->listeners[$eventClass]
            : [];
    }

    /**
     * @param string $eventClass
     * @param callable $listener
     * @return $this
     */
    public function addListener(string $eventClass, callable $listener): static
    {
        $this->listeners[$eventClass][] = $listener;

        return $this;
    }

    /**
     * @param string|null $eventClass
     * @return void
     */
    public function clearListeners(?string $eventClass = null): void
    {
        if ($eventClass === null) {
            $this->listeners = [];
        } elseif (array_key_exists($eventClass, $this->listeners)) {
            unset($this->listeners[$eventClass]);
        }
    }
}
