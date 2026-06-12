<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Http;

use Psr\Http\Message\MessageInterface;

class Message implements MessageInterface
{
    use MessageTrait;
}
