<?php
/**
 * LoggerAware trait file.
 *
 * @package   Atan\Core
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atan\Core;

/** PSR-3 use block. */
use Psr\Log\LoggerAwareTrait;

trait LoggerAware
{
    use LoggerAwareTrait;

    /**
     * Access the `log()` method from the logger.
     *
     * @param string $level   Use `LogLevel` constants.
     * @param string $message Message to log.
     * @param array  $context Context array for the message.
     *
     * @return void
     */
    protected function log(string $level, string $message, array $context = [])
    {
        if (isset($this->logger)) {
            $message = get_class($this) . ': ' . $message;
            $this->logger->log($level, $message, $context);
        }
    }
}
