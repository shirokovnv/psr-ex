<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Event;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Shirokovnv\PsrEx\Event\EventDispatcher;
use Shirokovnv\PsrEx\Event\ListenerProvider;

abstract class EventTestCase extends TestCase
{
    /**
     * @var ListenerProviderInterface
     */
    protected ListenerProviderInterface $listenerProvider;

    /**
     * @var EventDispatcherInterface
     */
    protected EventDispatcherInterface $eventDispatcher;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->listenerProvider = new ListenerProvider();
        $this->eventDispatcher = new EventDispatcher($this->listenerProvider);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->eventDispatcher);
        unset($this->listenerProvider);
    }
}
