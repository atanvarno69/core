<?php
/**
 * Created by PhpStorm.
 * User: atan
 * Date: 08/01/2017
 * Time: 03:10 PM
 */

namespace Atan\Core\Test;

/** SPL use block. */
use RuntimeException;

/** PSR-7 use block. */
use Psr\Http\Message\{
    ResponseInterface,
    StreamInterface
};

/** PHPUnit use block. */
use PHPUnit_Framework_TestCase as TestCase;

/** Package use block. */
use Atan\Core\{
    Emitter,
    HeaderSentOutput,
    Output
};

include __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';

class EmitterTest extends TestCase
{
    private $emitter;

    public function setUp()
    {
        Output::reset();
        $this->emitter = new Emitter();
    }

    public function testEmitResponseHeaders()
    {
        $headers = [
            'Content-Type' => ['text/plain'],
            'Content-Length' => ['8'],
        ];
        $response = $this->buildResponse($headers);
        $this->headersSentWillReturn(false);
        ob_start();
        $this->emitter->emit($response);
        ob_end_clean();
        $this->assertContains('HTTP/1.1 200 OK', Output::$headers);
        $this->assertContains('Content-Type: text/plain', Output::$headers);
        $this->assertContains('Content-Length: 8', Output::$headers);
    }

    public function testEmitMessageBody()
    {
        $this->headersSentWillReturn(false);
        $headers = [
            'Content-Type' => ['text/plain'],
            'Content-Length' => ['8'],
        ];
        $response = $this->buildResponse($headers);
        $this->expectOutputString('Content!');
        $this->emitter->emit($response);
    }

    public function testEmitFlushes()
    {
        ob_start();
        echo 'Level 1';
        $this->headersSentWillReturn(false);
        $headers = [
            'Content-Type' => ['text/plain'],
            'Content-Length' => ['8'],
        ];
        $response = $this->buildResponse($headers);
        $this->expectOutputString('Level 1Content!');
        $this->emitter->emit($response, 1);
    }

    public function testEmitThrowsRuntimeExceptionIfHeadersAlreadySent()
    {
        $this->headersSentWillReturn(true);
        $response = $this->buildResponse();
        $this->expectException(RuntimeException::class);
        $this->emitter->emit($response);
    }

    private function headersSentWillReturn(bool $bool)
    {
        HeaderSentOutput::setValue($bool);
    }

    private function buildResponse(
        array $headers = [],
        string $version = '1.1',
        int $code = 200,
        string $phrase = 'OK',
        string $content = 'Content!'
    ) {
        $body = $this->getMockBuilder(StreamInterface::class)->getMock();
        $body->method('__toString')->willReturn($content);
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $response->method('getProtocolVersion')->willReturn($version);
        $response->method('getStatusCode')->willReturn($code);
        $response->method('getReasonPhrase')->willReturn($phrase);
        $response->method('getHeaders')->willReturn($headers);
        $response->method('getBody')->willReturn($body);
        return $response;
    }
}
