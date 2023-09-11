<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Container;

use PHPUnit\Framework\TestCase;
use Shirokovnv\PsrEx\Container\Container;

abstract class ContainerTestCase extends TestCase
{
    /**
     * @var Container
     */
    protected Container $container;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->container);
        parent::tearDown();
    }
}
