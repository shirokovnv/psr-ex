<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Unit\Container;

use Shirokovnv\PsrEx\Container\Exceptions\CyclicalReferenceException;
use Shirokovnv\PsrEx\Tests\Stubs\Services\Loop\ServiceOne;
use Shirokovnv\PsrEx\Tests\Stubs\Services\Loop\ServiceTwo;

class ServiceLoopTestCase extends ContainerTestCase
{
    /**
     * @return void
     */
    public function testResolvingDependencyLoop(): void
    {
        $this->container->set(ServiceOne::class, ServiceOne::class);
        $this->container->set(ServiceTwo::class, ServiceTwo::class);

        $this->expectException(CyclicalReferenceException::class);
        $this->container->get(ServiceOne::class);
        $this->container->get(ServiceTwo::class);
    }
}
