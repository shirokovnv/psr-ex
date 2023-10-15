<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareStack implements RequestHandlerInterface
{
    /**
     * @var array<MiddlewareInterface>
     */
    private array $middlewareStack;

    /**
     * @var RequestHandlerInterface
     */
    private RequestHandlerInterface $handler;

    /**
     * @param array<MiddlewareInterface> $middlewareStack
     * @param RequestHandlerInterface $handler
     */
    public function __construct(
        array $middlewareStack,
        RequestHandlerInterface $handler
    ) {
        $this->middlewareStack = $middlewareStack;
        $this->handler = $handler;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = array_shift($this->middlewareStack);

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }

        return $this->handler->handle($request);
    }
}
