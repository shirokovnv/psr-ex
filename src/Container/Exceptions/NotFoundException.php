<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Container\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class NotFoundException extends \Exception implements ContainerExceptionInterface
{
}
