# PSR Examples

![ci.yml][link-ci]

[PSR-11][link-psr11] container implementation

## Usage

```php

use Shirokovnv\PsrEx\Container\Container;

$container = new Container();

// Put into the container
$container->set(ServiceInterface::class, Service::class);

// Get from container
// assert instanceOf Service::class
$instance = $container->get(ServiceInterface::class);

// Check existence
if ($container->has(ServiceInterface::class)) {
    // do stuff
}
```

## Testing

``` bash
$ composer test
```

## License

MIT. Please see the [license file](LICENSE.md) for more information.

[link-ci]: https://github.com/shirokovnv/psr-ex/actions/workflows/ci.yml/badge.svg
[link-psr11]: https://www.php-fig.org/psr/psr-11/
[link-author]: https://github.com/shirokovnv
[link-contributors]: ../../contributors
