<?php
/**
 * Core class file.
 *
 * @package   Atan\Core
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atan\Core;

abstract class Core
{
    /**
     * Sets error display, reporting and logging.
     *
     * @param bool        $displayErrors Whether errors should be displayed.
     * @param int         $level         Use the PHP error function
     *      constants http://php.net/manual/en/errorfunc.constants.php or 0.
     * @param string|null $logPath       Path to log file or `null`.
     *
     * @return void
     */
    public static function error(
        bool $displayErrors = false,
        int $level = E_ALL,
        string $logPath = null
    ) {
        $logPath = $logPath ?? false;
        error_reporting($level);
        ini_set('display_errors', $displayErrors);
        ini_set('display_startup_errors', $displayErrors);
        ini_set('log_errors', (bool) $logPath);
        ini_set('error_log', $logPath);
    }

    /**
     * Concatenates given pieces with DIRECTORY_SEPARATOR to make a file path.
     *
     * Resolves `..`, etc if resulting path exists.
     *
     * @param string[] ...$pieces Bits to concatenate with DIRECTORY_SEPARATOR.
     *
     * @return string Concatenated string.
     */
    public static function path(string ...$pieces): string
    {
        $return = implode(DIRECTORY_SEPARATOR, $pieces);
        if (file_exists($return)) {
            $return = realpath($return);
        }
        return $return;
    }
}
