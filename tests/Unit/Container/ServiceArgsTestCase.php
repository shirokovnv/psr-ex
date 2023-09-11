<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Container;

use Shirokovnv\PsrEx\Container\Exceptions\ContainerException;
use Shirokovnv\PsrEx\Tests\Stubs\Services\Parametric\ServiceWithArgs;
use Shirokovnv\PsrEx\Tests\Stubs\Services\Parametric\ServiceWithDefaultArgs;
use Shirokovnv\PsrEx\Tests\Stubs\Services\Parametric\ServiceWithoutArgs;
use Shirokovnv\PsrEx\Tests\Stubs\Services\Parametric\ServiceWithTypedArgs;

class ServiceArgsTestCase extends ContainerTestCase
{
    /**
     * @return void
     */
    public function testResolvingServiceWithoutArgs(): void
    {
        $this->container->set(ServiceWithoutArgs::class, ServiceWithoutArgs::class);

        $instance = $this->container->get(ServiceWithoutArgs::class);
        $this->assertInstanceOf(ServiceWithoutArgs::class, $instance);
    }

    /**
     * @return void
     */
    public function testResolvingServiceWithArgs(): void
    {
        $this->container->set(ServiceWithArgs::class, ServiceWithArgs::class);

        $this->expectException(ContainerException::class);
        $this->container->get(ServiceWithArgs::class);
    }

    /**
     * @return void
     */
    public function testResolvingServiceWithTypedArgs(): void
    {
        $this->container->set(ServiceWithTypedArgs::class, ServiceWithTypedArgs::class);

        $this->expectException(ContainerException::class);
        $this->container->get(ServiceWithTypedArgs::class);
    }

    /**
     * @return void
     */
    public function testResolvingServiceWithDefaultArgs(): void
    {
        $this->container->set(ServiceWithDefaultArgs::class, ServiceWithDefaultArgs::class);
        /** @var ServiceWithDefaultArgs $instance */
        $instance = $this->container->get(ServiceWithDefaultArgs::class);
        $this->assertInstanceOf(ServiceWithDefaultArgs::class, $instance);
        $this->assertEmpty($instance->param1);
        $this->assertNull($instance->param2);
    }
}
