<?php
/**
 * Emitter class file.
 *
 * @package   Atan\Core
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atan\Core;

/** SPL use block. */
use RuntimeException;

/** PSR-7 use block. */
use Psr\Http\Message\ResponseInterface;

class Emitter
{
    /**
     * Emits a PSR-7 response.
     *
     * Sends status line and other headers and echoes the body.
     *
     * @param ResponseInterface $response       PSR-7 response to emit.
     * @param int|null          $maxBufferLevel Maximum buffer level to flush.
     *
     * @return void
     */
    public function emit(
        ResponseInterface $response,
        int $maxBufferLevel = null
    ) {
        if (headers_sent()) {
            $msg = 'Unable to emit response; HTTP headers already sent';
            throw new RuntimeException($msg);
        }
        $this->emitStatusLine($response);
        $this->emitHeaders($response);
        $this->flush($maxBufferLevel);
        $this->emitBody($response);
    }

    private function emitBody(ResponseInterface $response)
    {
        echo $response->getBody();
    }

    private function emitHeaders(ResponseInterface $response)
    {
        foreach ($response->getHeaders() as $header => $values) {
            $name  = $this->filterHeader($header);
            $first = true;
            foreach ($values as $value) {
                header($name . ': ' . $value, $first);
                $first = false;
            }
        }
    }

    private function emitStatusLine(ResponseInterface $response)
    {
        $open = 'HTTP/'
            . $response->getProtocolVersion()
            . ' '
            . (string) $response->getStatusCode();
        $phrase = ($response->getReasonPhrase())
            ? ' ' . $response->getReasonPhrase()
            : '';
        header($open . $phrase);
    }

    private function flush($maxBufferLevel = null)
    {
        $bufferLevel = $maxBufferLevel ?? ob_get_level();
        while (ob_get_level() > $bufferLevel) {
            ob_end_flush();
        }
    }

    private function filterHeader(string $header): string
    {
        $filtered = ucwords(str_replace('-', ' ', $header));
        return str_replace(' ', '-', $filtered);
    }
}
