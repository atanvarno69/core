# Atan\Core\Core
Helper functions.

```php
abstract Atan\Core\Core {
    
    public static error(
        bool $displayErrors = false,
        int $level = E_ALL,
        string $logPath = null
    ): void
    
    public static path(
        string ...$pieces
    ): string 
}
```
* [Core::error](#error)
* [Core::path](#path)

This class is `abstract` as it is not intended to be instantiated.

## error
Sets error display, reporting and logging.
```php
static error(bool $displayErrors = false, int $level = E_ALL, string $logPath = null): void
```
Controls whether errors should be displayed, what level of error should be reported and logging of errors.

### Parameters
#### displayErrors
`bool`: Passed to `ini_set('display_errors')` and `ini_set('display_startup_errors')`. Optional, with default value: `false`.

#### level
`int`: Passed to [`error_reporting()`](http://php.net/manual/en/function.error-reporting.php). Use PHP's [error reporting constants](http://php.net/manual/en/errorfunc.constants.php), or `0` to disable error reporting. Optional, with default value: `E_ALL`.

#### logPath
`string`: The path to a log file, passed to `ini_set('error_log')`. If a value is given, `true` is passed to `ini_set('log_errors')`, otherwise `false` is passed. Optional, with default value: `null`.

### Throws
None.

### Returns
None.

## path
Concatenates given pieces with `DIRECTORY_SEPARATOR` to make a file path.
```php
static path(STRING ...$pieces): string
```
Resolves `..`, etc if resulting path exists.

### Parameters
#### pieces
`string`: Pieces to concatenate with `DIRECTORY_SEPARATOR`.

### Throws
None.

### Returns
`string`: Concatenated string.
