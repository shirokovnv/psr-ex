<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Services\Chain;

use Shirokovnv\PsrEx\Tests\Stubs\Services\ServiceInterface;

class ServiceB implements ServiceInterface
{
    /**
     * @param ServiceC $serviceC
     */
    public function __construct(public ServiceC $serviceC)
    {
    }
}
