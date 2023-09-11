<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Container\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class CyclicalReferenceException extends \Exception implements ContainerExceptionInterface
{
}
