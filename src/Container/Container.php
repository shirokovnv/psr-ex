<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Container;

use Psr\Container\ContainerInterface;
use Shirokovnv\PsrEx\Container\Exceptions\ContainerException;
use Shirokovnv\PsrEx\Container\Exceptions\CyclicalReferenceException;
use Shirokovnv\PsrEx\Container\Exceptions\NotFoundException;

class Container implements ContainerInterface
{
    /**
     * @var array<string, callable|string>
     */
    private array $entries;

    /**
     * @param string $id
     * @return mixed
     *
     * @throws ContainerException
     * @throws CyclicalReferenceException
     * @throws NotFoundException
     */
    public function get(string $id): mixed
    {
        $entry = $this->has($id) ? $this->entries[$id] : $id;

        return $this->resolve($entry);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    /**
     * @param class-string $id
     * @param callable|string $concrete
     *
     * @return Container
     */
    public function set(string $id, callable|string $concrete): Container
    {
        $this->entries[$id] = $concrete;

        return $this;
    }

    /**
     * @param callable|string $concrete
     * @param array<string> $callstack
     * @return mixed
     *
     * @throws ContainerException
     * @throws CyclicalReferenceException
     * @throws NotFoundException
     */
    protected function resolve(callable|string $concrete, array $callstack = []): mixed
    {
        if (is_callable($concrete)) {
            return $concrete($this);
        }

        try {
            /** @phpstan-ignore-next-line */
            $reflectionClass = new \ReflectionClass($concrete);
        } catch (\ReflectionException $exception) {
            throw new NotFoundException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }

        if (! $reflectionClass->isInstantiable()) {
            throw new ContainerException(
                sprintf('Class %s is not instantiable', $concrete)
            );
        }

        if (in_array($concrete, $callstack)) {
            throw new CyclicalReferenceException(
                sprintf('Class %s has cyclical reference', $concrete)
            );
        }

        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return new $concrete();
        }

        $parameters = $constructor->getParameters();

        if (count($parameters) === 0) {
            return new $concrete();
        }

        $args = array_map(function (\ReflectionParameter $param) use ($callstack, $concrete) {

            $name = $param->getName();
            $type = $param->getType();

            if ($type === null) {
                return $param->isDefaultValueAvailable()
                    ? $param->getDefaultValue()
                    : throw new ContainerException(
                        sprintf(
                            'Unable to resolve class %s dependency %s due to missing type hint',
                            $concrete,
                            $name
                        )
                    );
            }

            if ($type instanceof \ReflectionUnionType) {
                throw new ContainerException(
                    sprintf(
                        'Unable to resolve class %s dependency %s due to union type',
                        $concrete,
                        $name
                    )
                );
            }

            if ($type instanceof \ReflectionNamedType) {

                if ($type->isBuiltin() && $param->isDefaultValueAvailable()) {
                    return $param->getDefaultValue();
                }

                if (! $type->isBuiltin()) {
                    return $this->resolve(
                        $this->entries[$type->getName()] ?? $type->getName(),
                        $callstack + [$concrete]
                    );
                }
            }

            throw new ContainerException(
                sprintf(
                    'Unable to resolve class %s dependency %s',
                    $concrete,
                    $name
                )
            );

        }, $parameters);

        try {
            return $reflectionClass->newInstanceArgs($args);
        } catch (\ReflectionException $exception) {
            throw new ContainerException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }
}
