<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Services\Loop;

class ServiceOne
{
    /**
     * @param ServiceTwo $serviceTwo
     */
    public function __construct(ServiceTwo $serviceTwo)
    {
    }
}
