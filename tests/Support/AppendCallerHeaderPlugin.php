<?php

declare(strict_types=1);

namespace Gamez\Mite\Tests\Support;

use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
final class AppendCallerHeaderPlugin implements Plugin
{
    /**
     * @param non-empty-string $header
     * @param non-empty-string $namespace
     */
    public function __construct(
        private readonly string $header,
        private readonly string $namespace,
    ) {
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $backtrace = debug_backtrace();

        $method = '';

        foreach ($backtrace as $item) {
            $class = $item['class'] ?? '';
            $function = $item['function'];

            if (str_contains($class, $this->namespace)) {
                $method = $class.'::'.$function;

                break;
            }
        }

        if ('' === $method) {
            return $next($request);
        }

        $request = $request->withAddedHeader($this->header, $method);

        return $next($request)->then(function (ResponseInterface $response) use ($method) {
            if (!$response->hasHeader($this->header)) {
                $response = $response->withAddedHeader($this->header, $method);
            }

            return $response;
        });
    }
}
