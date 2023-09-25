<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Events;

use Psr\EventDispatcher\StoppableEventInterface;

class BirdMessageEvent extends AbstractMessageEvent implements StoppableEventInterface
{
    /**
     * @return void
     */
    public function sayHello(): void
    {
        echo 'cra';
    }

    /**
     * @return bool
     */
    public function isPropagationStopped(): bool
    {
        return true;
    }
}
