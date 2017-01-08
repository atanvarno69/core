<?php
/**
 * ResponseProviderTest class file.
 *
 * @package   Atan\Core
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atan\Core\Test;

/** PSR-7 use block. */
use Psr\Http\Message\ResponseInterface;

/** PSR-17 use block. */
use Interop\Http\Factory\{
    ResponseFactoryInterface,
    StreamFactoryInterface
};

/** PHPUnit use block. */
use PHPUnit_Framework_TestCase as TestCase;

/** Factory use block. */
use Http\Factory\Diactoros\{
    ResponseFactory,
    StreamFactory
};

/** Package use block. */
use Atan\Core\ResponseProvider;

class ResponseProviderMock
{
    use ResponseProvider;
}

class ResponseProviderTest extends TestCase
{
    private $responseProvider;

    public function setUp()
    {
        $this->responseProvider = new ResponseProviderMock(new StreamFactory(), new ResponseFactory());
    }

    public function testConstructor()
    {
        $responseProvider = new ResponseProviderMock(new StreamFactory(), new ResponseFactory());
        $this->assertObjectHasAttribute('responseFactory', $responseProvider);
        $this->assertObjectHasAttribute('streamFactory', $responseProvider);
    }

    public function testBuildPrototypeResponse()
    {
        $response = $this->callMethod($this->responseProvider, 'buildPrototypeResponse');
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertTrue($response->getBody()->isReadable());
        $this->assertTrue($response->getBody()->isSeekable());
        $this->assertTrue($response->getBody()->isWritable());
    }

    public function testBuildErrorResponseDefault()
    {
        $response = $this->callMethod($this->responseProvider, 'buildErrorResponse');
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertTrue($response->getBody()->isReadable());
        $this->assertTrue($response->getBody()->isSeekable());
        $this->assertTrue($response->getBody()->isWritable());
        $this->assertSame(500, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Cache-Control'));
        $this->assertSame('no-cache', $response->getHeaderLine('Cache-Control'));
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertSame('text/plain; charset=UTF-8', $response->getHeaderLine('Content-Type'));
        $this->assertTrue($response->hasHeader('Content-Length'));
        $this->assertSame('25', $response->getHeaderLine('Content-Length'));
        $this->assertSame('500 Internal Server Error', (string) $response->getBody());
    }

    public function testBuildErrorResponseFirstParameter()
    {
        $response = $this->callMethod($this->responseProvider, 'buildErrorResponse', [501]);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertTrue($response->getBody()->isReadable());
        $this->assertTrue($response->getBody()->isSeekable());
        $this->assertTrue($response->getBody()->isWritable());
        $this->assertSame(501, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Cache-Control'));
        $this->assertSame('no-cache', $response->getHeaderLine('Cache-Control'));
        $this->assertTrue($response->hasHeader('Content-Type'));
        $this->assertSame('text/plain; charset=UTF-8', $response->getHeaderLine('Content-Type'));
        $this->assertTrue($response->hasHeader('Content-Length'));
        $this->assertSame('19', $response->getHeaderLine('Content-Length'));
        $this->assertSame('501 Not Implemented', (string) $response->getBody());
    }

    public function testGetResponseFactory()
    {
        $factory = $this->callMethod($this->responseProvider, 'getResponseFactory');
        $this->assertInstanceOf(ResponseFactoryInterface::class, $factory);
    }

    public function testGetStreamFactory()
    {
        $factory = $this->callMethod($this->responseProvider, 'getStreamFactory');
        $this->assertInstanceOf(StreamFactoryInterface::class, $factory);
    }

    private function callMethod($obj, string $name, array $args = [])
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }
}

