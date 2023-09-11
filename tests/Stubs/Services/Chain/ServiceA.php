<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Services\Chain;

use Shirokovnv\PsrEx\Tests\Stubs\Services\ServiceInterface;

class ServiceA implements ServiceInterface
{
    /**
     * @param ServiceB $serviceB
     */
    public function __construct(public ServiceB $serviceB)
    {
    }
}
