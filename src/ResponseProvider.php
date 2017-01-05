<?php
/**
 * ResponseProvider trait file.
 *
 * @package   Atan\Core
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atan\Core;

/** PSR-7 use block. */
use Psr\Http\Message\ResponseInterface;

/** PSR-17 use block. */
use Interop\Http\Factory\{
    ResponseFactoryInterface,
    StreamFactoryInterface
};

trait ResponseProvider
{
    /**
     * @var ResponseFactoryInterface $responseFactory PSR-17 response factory.
     * @var StreamFactoryInterface   $streamFactory   PSR-17 stream factory.
     */
    protected $responseFactory, $streamFactory;

    /**
     * ResponseProvider constructor.
     *
     * @param StreamFactoryInterface   $streamFactory   PSR-17 response factory.
     * @param ResponseFactoryInterface $responseFactory PSR-17 stream factory.
     */
    public function __construct(
        StreamFactoryInterface $streamFactory,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->streamFactory = $streamFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Provides a basic HTTP error response.
     *
     * @param int $code HTTP error code.
     *
     * @return ResponseInterface PSR-7 response.
     */
    protected function buildErrorResponse(int $code = 500): ResponseInterface
    {
        $response = $this->getPrototypeResponse()
            ->withStatus($code)
            ->withHeader('Cache-Control', 'no-cache')
            ->withHeader('Content-Type', 'text/plain; charset=UTF-8');
        $body = (string) $code . ' ' . $response->getReasonPhrase();
        $response->getBody()->write($body);
        $response->getBody()->rewind();
        return $response->withHeader(
            'Content-Length',
            (string) $response->getBody()->getSize()
        );
    }

    /**
     * Provides a PSR-7 response with a readable, writable and seekable stream
     * body.
     *
     * @return ResponseInterface PSR-7 response.
     */
    protected function getPrototypeResponse(): ResponseInterface
    {
        $stream = $this->getStreamFactory()->createStreamFromResource(
            fopen('php://temp', 'r+')
        );
        return $this->getResponseFactory()->createResponse()->withBody($stream);
    }

    /**
     * Provides a PSR-17 response factory.
     *
     * For use when `getPrototypeResponse()` does not fulfil your use case.
     *
     * @return ResponseFactoryInterface PSR-17 response factory.
     */
    protected function getResponseFactory(): ResponseFactoryInterface
    {
        return $this->responseFactory;
    }

    /**
     * Provides a PSR-17 stream factory.
     *
     * For use when `getPrototypeResponse()` does not fulfil your use case.
     *
     * @return StreamFactoryInterface PSR-17 stream factory.
     */
    protected function getStreamFactory(): StreamFactoryInterface
    {
        return $this->streamFactory;
    }
}
