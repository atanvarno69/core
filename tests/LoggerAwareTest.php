<?php
/**
 * LoggerAwareTest class file.
 *
 * @package   Atan\Core
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atan\Core\Test;

/** PSR-3 use block. */
use Psr\Log\{
    LoggerInterface,
    LogLevel
};

/** PHPUnit use block. */
use PHPUnit_Framework_TestCase as TestCase;

/** Package use block. */
use Atan\Core\LoggerAware;

class LoggerAwareTest extends TestCase
{
    public function testLogWithLogger()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $loggerAware = $this->getMockForTrait(LoggerAware::class);
        $this->setProperty($loggerAware, 'logger', $logger);
        $logger->expects($this->once())
            ->method('log')
            ->with($this->equalTo(LogLevel::ERROR), $this->stringContains(': message'), $this->equalTo([]));
        $this->callMethod($loggerAware, 'log', [LogLevel::ERROR, 'message']);
    }

    private function setProperty($obj, string $name, $value)
    {
        $reflection = new \ReflectionClass($obj);
        $reflectionProperty = $reflection->getProperty($name);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($obj, $value);
    }

    private function callMethod($obj, string $name, array $args = [])
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
}
