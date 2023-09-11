<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Container;

use Shirokovnv\PsrEx\Tests\Stubs\Services\Chain\ServiceA;
use Shirokovnv\PsrEx\Tests\Stubs\Services\Chain\ServiceB;
use Shirokovnv\PsrEx\Tests\Stubs\Services\Chain\ServiceC;

class ServiceChainTestCase extends ContainerTestCase
{
    /**
     * @return void
     */
    public function testResolvingServiceChain(): void
    {
        $this->container->set(ServiceA::class, ServiceA::class);
        $this->container->set(ServiceB::class, ServiceB::class);
        $this->container->set(ServiceC::class, ServiceC::class);

        $instance = $this->container->get(ServiceA::class);
        $this->assertInstanceOf(ServiceA::class, $instance);
    }
}
