<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Stubs\Services\Parametric;

class ServiceWithDefaultArgs
{
    /**
     * @param string $param1
     * @param $param2
     */
    public function __construct(public string $param1 = '', public $param2 = null)
    {
    }
}
