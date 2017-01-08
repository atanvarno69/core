# Atan\Core
Framework classes and helper functions.

## Requirements
**PHP >= 7.0** is required, but the latest stable version of PHP is recommended.

## Installation
Add the following to your `composer.json`:
```json
"require": {
    "atan/core": "^1.0.0"
},
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/atanvarno69/core"
    }
]
```
Then:
```bash
$ composer install
# or
$ composer update
```

## Basic Usage
The included classes are primarily intended for use by [`atan\framework`](https://github.com/atanvarno69/framework/). However, the classes may be generally useful to you.

### Catcher
`Atan\Core\Catcher` is PSR-15 middleware intended to sit at the start of a middleware queue, it will catch any `Throwable` from the queue and return a basic error response, without exposing any developer information.

Instantiate the class and add it to the top start of your middleware queue.

### Core
`Atan\Core\Core` is a collection of useful static functions.

#### Core::error()
Controls various PHP error and logging functions. When called without parameters, it sets the following defaults:
```php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 0);
ini_set('error_log', '');
```
See the [API](https://github.com/atanvarno69/core/blob/master/docs/Core.md#coreerror) for usage details.

#### Core::path()
Accepts an arbitrary number of strings and concatenates them with `DIRECTORY_SEPARATOR`. If the resulting path is real, it resolves `..`, etc.
```php
$path = Atan\Core\Core::path(dirname(__FILE__, 2), 'dirName', 'subDirName', 'file.php');
```

### Emitter
`Atan\Core\Emitter` emits a PSR-7 response.
```php
$emitter = new Atan\Core\Emitter();

// Emit response status line, headers and body:
$emitter->emit($response);
```

### Preparer
`Atan\Core\Preparer` is PSR-15 middleware intended to sit near the start of a middleware queue, it does final preparation on the response to ensure it complies with the HTTP specification before it is emitted.

Instantiate the class and add it to the top start of your middleware queue.

## Full API
See [API](https://github.com/atanvarno69/core/blob/master/docs/API.md).
