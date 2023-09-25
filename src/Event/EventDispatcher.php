<?php

declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @param ListenerProviderInterface $listenerProvider
     */
    public function __construct(readonly private ListenerProviderInterface $listenerProvider)
    {
    }

    /**
     * @param object $event
     * @return object|StoppableEventInterface
     */
    public function dispatch(object $event)
    {
        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            return $event;
        }

        $listeners = $this->listenerProvider->getListenersForEvent($event);
        foreach($listeners as $listener) {
            if (is_callable($listener)) {
                $listener($event);
            }
        }

        return $event;
    }
}
