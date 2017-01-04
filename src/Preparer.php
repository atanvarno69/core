<?php
/**
 * Preparer class file.
 *
 * @package   Atan\Core
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atan\Core;

/** PSR-7 use block. */
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};

/** PSR-15 use block. */
use Interop\Http\ServerMiddleware\{
    DelegateInterface,
    MiddlewareInterface
};

class Preparer implements MiddlewareInterface
{
    use ResponseProvider;

    /**
     * Prepares a PSR-7 response according to a request inline with the HTTP
     * specification.
     *
     * @param ResponseInterface      $response PSR-7 response to prepare.
     * @param ServerRequestInterface $request  PSR-7 request.
     *
     * @return ResponseInterface Prepared PSR-7 response.
     */
    public function prepare(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): ResponseInterface {
        $response = ($this->isInformational($response))
            ? $this->prepareInformational($response)
            : $this->prepareNonInformational($response, $request);
        return $this->prepareAll($response, $request);
    }

    /**
     * Prepares a response as middleware.
     *
     * @param ServerRequestInterface $request  PSR-7 request to prepare against.
     * @param DelegateInterface      $delegate
     *
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        DelegateInterface $delegate
    ): ResponseInterface {
        $response = $delegate->process($request);
        return $this->prepare($response, $request);
    }

    private function isContentAcceptable(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): bool {
        if (!$request->hasHeader('Accept')) {
            return true;
        }
        $acceptable = $request->getHeader('Accept');
        $sending = $response->getHeader('Content-Type');
        if(in_array($sending[0], $acceptable)) {
            return true;
        }
        return false;
    }

    private function isInformational(ResponseInterface $response): bool
    {
        $codes = [100, 101, 102, 204, 304];
        return in_array($response->getStatusCode(), $codes);
    }

    private function prepareAll(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): ResponseInterface {
        if (!$response->hasHeader('Date')) {
            $response = $response->withHeader(
                'Date',
                gmdate('D, d M Y H:i:s T')
            );
        }
        if ($response->getProtocolVersion() != $request->getProtocolVersion()) {
            $response = $response->withProtocolVersion(
                ($request->getProtocolVersion() == '1.1') ? '1.1' : '1.0'
            );
        }
        if ($response->getProtocolVersion() == '1.0'
            && $response->hasHeader('Cache-Control')
        ) {
            $cacheHeader = $response->getHeaderLine('Cache-Control');
            if (strpos($cacheHeader, 'no-cache') !== false) {
                $response = $response->withHeader('pragma', 'no-cache')
                    ->withHeader('expires', '-1');
            }
        }
        // Ensure IE over SSL compatibility
        if (!$response->hasHeader('Content-Disposition')) {
            return $response;
        }
        $disposition = $response->getHeaderLine('Content-Disposition');
        if (stripos($disposition, 'attachment') === false) {
            return $response;
        }
        $serverParams = $request->getServerParams();
        $userAgent = $serverParams['HTTP_USER_AGENT'];
        if (preg_match('/MSIE (.*?);/i', $userAgent, $match) !== 1) {
            return $response;
        }
        if (!isset($serverParams['HTTPS']) || $serverParams['HTTPS'] == 'off') {
            return $response;
        }
        if ((int) preg_replace('/(MSIE )(.*?);/', '$2', $match[0]) >= 9) {
            return $response;
        }
        return $response->withoutHeader('Cache-Control');
    }

    private function prepareInformational(
        ResponseInterface $response
    ): ResponseInterface {
        return $this->removeBody($response)
            ->withoutHeader('Content-Type')
            ->withoutHeader('Content-Length');
    }

    private function prepareNonInformational(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): ResponseInterface {
        if (!$response->hasHeader('Content-Type')) {
            $value = 'text/html; charset=UTF-8';
            $response = $response->withHeader('Content-Type', $value);
        }
        if (!$this->isContentAcceptable($response, $request)) {
            $response = $this->buildErrorResponse(406);
        }
        $contentType = $response->getHeaderLine('Content-Type');
        if (strpos($contentType, 'text/') === 0
            && strpos($contentType, 'charset') === false
        ) {
            $fullType = $contentType . '; charset=UTF-8';
            $response = $response->withHeader('Content-Type', $fullType);
        }
        if ($response->hasHeader('Transfer-Encoding')) {
            $response = $response->withoutHeader('Content-Length');
        } elseif (!$response->hasHeader('Content-Length')) {
            $size = (string) $response->getBody()->getSize();
            $response = $response->withHeader('Content-Length', $size);
        }
        if ($request->getMethod() == 'HEAD') {
            $response = $this->removeBody($response);
        }
        return $response;
    }

    private function removeBody(ResponseInterface $response): ResponseInterface
    {
        $body = $this->getStreamFactory()
            ->createStreamFromResource(fopen('php://temp', 'r+'));
        return $response->withBody($body);
    }
}
