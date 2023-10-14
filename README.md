# PSR Examples

![ci.yml][link-ci]

[PSR-3][link-psr3] logger implementation

[PSR-11][link-psr11] container implementation

[PSR-14][link-psr14] event dispatcher implementation

## Usage

### Logger

```php

use Shirokovnv\PsrEx\Log\Formatter\BaseFormatter;
use Shirokovnv\PsrEx\Log\Handler\FileHandler;
use Shirokovnv\PsrEx\Log\Logger;

$logger = new Logger(new FileHandler('log.txt'), new BaseFormatter());
$logger->log(\Psr\Log\LogLevel::DEBUG, 'debug message', [ 'context' => 'some context' ]);
```

### Container

```php

use Shirokovnv\PsrEx\Container\Container;

$container = new Container();

// Put into the container
$container->set(ServiceInterface::class, Service::class);

// Get from the container
// assert instanceOf Service::class
$instance = $container->get(ServiceInterface::class);

// Check existence
if ($container->has(ServiceInterface::class)) {
    // do stuff
}
```

### Events

```php

use Shirokovnv\PsrEx\Event\EventDispatcher;
use Shirokovnv\PsrEx\Event\ListenerProvider;

$event = new MyEvent();
$listener = new MyEventListener();

$listenerProvider = new ListenerProvider();
$eventDispatcher = new EventDispatcher($listenerProvider);

// Bind event and listener
$listenerProvider->addListener($event::class, $listener);

// Dispatch
$eventDispatcher->dispatch($event);

// Clear
$listenerProvider->clearListeners();
```

## Testing

``` bash
$ composer test
```

## License

MIT. Please see the [license file](LICENSE.md) for more information.

[link-ci]: https://github.com/shirokovnv/psr-ex/actions/workflows/ci.yml/badge.svg
[link-psr3]: https://www.php-fig.org/psr/psr-3/
[link-psr11]: https://www.php-fig.org/psr/psr-11/
[link-psr14]: https://www.php-fig.org/psr/psr-14/
[link-author]: https://github.com/shirokovnv
