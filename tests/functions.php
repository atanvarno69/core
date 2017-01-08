<?php
/**
 * functions file.
 *
 * @package   Atan\Core
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atan\Core;

class Output
{
    public static $headers;

    public static function reset()
    {
        self::$headers = [];
    }

    public static function push($header)
    {
        self::$headers[] = $header;
    }
}

class HeaderSentOutput
{
    public static $value;

    public static function setValue(bool $bool)
    {
        self::$value = $bool;
    }
}

function header(string $value, bool $replace = true, int $http_response_code = null)
{
    Output::push($value);
}

function headers_sent()
{
    return HeaderSentOutput::$value;
}
