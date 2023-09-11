<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Container;

use Shirokovnv\PsrEx\Container\Exceptions\NotFoundException;
use Shirokovnv\PsrEx\Tests\Stubs\Services\Service;
use Shirokovnv\PsrEx\Tests\Stubs\Services\ServiceInterface;

class GenericServiceTestCase extends ContainerTestCase
{
    /**
     * @return void
     */
    public function testInterfaceBinding(): void
    {
        $this->container->set(ServiceInterface::class, Service::class);

        $instance = $this->container->get(ServiceInterface::class);
        $this->assertInstanceOf(Service::class, $instance);
    }

    /**
     * @return void
     */
    public function testServiceNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->container->get('service');
    }
}
