<?php
/**
 * CoreTest class file.
 *
 * @package   Atan\Core
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atan\Core\Test;

/** SPL use block. */
use Error;

/** PHPUnit use block. */
use PHPUnit_Framework_TestCase as TestCase;

/** Package use block. */
use Atan\Core\Core;

class CoreTest extends TestCase
{
    public function testErrorDefaults()
    {
        error_reporting(0);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        ini_set('log_errors', 1);
        ini_set('error_log', __DIR__ . 'log');
        Core::error();
        $this->assertEquals(E_ALL, error_reporting());
        $this->assertEquals('', ini_get('display_errors'));
        $this->assertEquals('', ini_get('display_startup_errors'));
        $this->assertEquals('', ini_get('log_errors'));
        $this->assertEquals('', ini_get('error_log'));
    }

    public function testErrorFirstParameter()
    {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        Core::error(true);
        $this->assertEquals(1, ini_get('display_errors'));
        $this->assertEquals(1, ini_get('display_startup_errors'));
    }

    public function testErrorSecondParameter()
    {
        error_reporting(E_ALL);
        Core::error(false, E_ERROR);
        $this->assertEquals(E_ERROR, error_reporting());
    }

    public function testErrorThirdParameter()
    {
        ini_set('log_errors', 0);
        ini_set('error_log', '');
        Core::error(false, E_ALL, __DIR__ . 'log');
        $this->assertEquals(1, ini_get('log_errors'));
        $this->assertEquals(__DIR__ . 'log', ini_get('error_log'));
    }

    public function testPathWithNonRealInput()
    {
        $expected = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'notRealName';
        $path = Core::path(__DIR__, '..', 'notRealName');
        $this->assertEquals($expected, $path);
    }

    public function testPathWithRealInput()
    {
        $expected = realpath(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'core');
        $path = Core::path(__DIR__, '..', '..', 'core');
        $this->assertEquals($expected, $path);
    }
}
