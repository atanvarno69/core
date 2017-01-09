<?php
/**
 * Catcher class file.
 *
 * @package   Atan\Core
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atan\Core;

/** SPL use block. */
use InvalidArgumentException, Throwable, UnexpectedValueException;

/** PSR-3 use block. */
use Psr\Log\{
    LoggerInterface,
    LogLevel
};

/** PSR-7 use block. */
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};

/** PSE-15 use block. */
use Interop\Http\ServerMiddleware\{
    DelegateInterface,
    MiddlewareInterface
};

/** PSR-17 use block. */
use Interop\Http\Factory\{
    ResponseFactoryInterface,
    StreamFactoryInterface
};

class Catcher implements LoggerAwareInterface, MiddlewareInterface
{
    /** Trait use block. */
    use LoggerAware;
    use ResponseProvider {
        ResponseProvider::__construct as parentConstruct;
    }

    /** @var callable[] $handlers Array of user defined error handlers. */
    private $handlers;

    /**
     * Catcher constructor.
     *
     * Requires PSR-17 stream and response factories.
     *
     * Optionally accepts an array of user defined error handlers. The array
     * key is the error code to handle and the value is a callable. Callables
     * must accept a Throwable as their only argument and must return a PSR-7
     * response object. Caught Throwables without a code are treated as if
     * they had code 500.
     *
     * Optionally accepts a PSR-3 logger.
     *
     * @param StreamFactoryInterface   $streamFactory   PSR-17 stream factory.
     * @param ResponseFactoryInterface $responseFactory PSR-17 response factory.
     * @param callable[]               $customHandlers  Error handlers.
     * @param LoggerInterface|null     $logger          PSR-3 logger.
     */
    public function __construct(
        StreamFactoryInterface $streamFactory,
        ResponseFactoryInterface $responseFactory,
        array $customHandlers = [],
        LoggerInterface $logger = null
    ) {
        $this->parentConstruct($streamFactory, $responseFactory);
        foreach ($customHandlers as $key => $value) {
            if (!is_int($key)) {
                $msg = 'Handlers must be indexed by integers';
                throw new InvalidArgumentException($msg);
            }
            if (!is_callable($value)) {
                $msg = 'Handler values must be callable';
                throw new InvalidArgumentException($msg);
            }
            $this->handlers[$key] = $value;
        }
        $this->setLogger($logger);
    }

    /**
     * If any later middleware throws a Throwable, returns an error response.
     * Otherwise returns the response from the later middleware.
     *
     * @param ServerRequestInterface $request  PSR-7 request.
     * @param DelegateInterface      $delegate PSR-15 delegate.
     *
     * @return ResponseInterface PSR-7 response.
     */
    public function process(
        ServerRequestInterface $request,
        DelegateInterface $delegate
    ): ResponseInterface {
        try {
            $response = $delegate->process($request);
        } catch (Throwable $caught) {
            $code = ($caught->getCode() == 0) ? 500 : $caught->getCode();
            $msg = 'Handled ' . get_class($caught) . '(' . $code . ')';
            $this->log(LogLevel::WARNING, $msg, ['exception' => $caught]);
            $handler = $this->handlers[$code] ?? [$this, 'handleGeneric'];
            try {
                $response = call_user_func($handler, $caught);
                if (!$response instanceof ResponseInterface) {
                    $msg = 'Handler must return a PSR-7 response';
                    throw new UnexpectedValueException($msg);
                }
            } catch (Throwable $error) {
                $msg = $code . ' callable is invalid; generic 500 error served';
                $this->log(LogLevel::ERROR, $msg, ['exception' => $error]);
                $response = $this->handleGeneric($caught);
            }
        }
        return $response;
    }

    private function handleGeneric(Throwable $caught): ResponseInterface
    {
        $code = ($caught->getCode() == 0) ? 500 : $caught->getCode();
        return $this->buildErrorResponse($code);
    }
}
