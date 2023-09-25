<?php

declare(strict_types=1);

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @param array<string, callable[]> $listeners
     */
    public function __construct(private array $listeners = [])
    {
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
