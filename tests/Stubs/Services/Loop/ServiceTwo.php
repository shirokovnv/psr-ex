<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Services\Loop;

class ServiceTwo
{
    /**
     * @param ServiceOne $serviceOne
     */
    public function __construct(public ServiceOne $serviceOne)
    {
    }
}
