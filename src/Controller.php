<?php
/**
 * Controller abstract class file.
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

abstract class Controller implements MiddlewareInterface
{
    /** Trait use block. */
    use ResponseProvider;

    /**
     * @param ServerRequestInterface $request  PSR-7 request.
     * @param DelegateInterface      $delegate PSR-15 delegate.
     *
     * @return ResponseInterface PSR-7 response.
     */
    abstract public function process(
        ServerRequestInterface $request,
        DelegateInterface $delegate
    ): ResponseInterface;
}
