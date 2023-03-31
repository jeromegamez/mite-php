<?php

declare(strict_types=1);

namespace Gamez\Mite\Tests\Support;

use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class AppendCallerHeaderPlugin implements Plugin
{
    public function __construct(
        private readonly string $header,
        private readonly string $namespace,
    ) {
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $backtrace = debug_backtrace();

        $classAndMethod = '';

        foreach ($backtrace as $item) {
            if (str_contains($item['class'], $this->namespace)) {
                $classAndMethod = $item['class'].'::'.$item['function'];
                break;
            }
        }

        if ($classAndMethod === '') {
            return $next($request);
        }

        $request = $request->withAddedHeader($this->header, $classAndMethod);

        return $next($request)->then(function (ResponseInterface $response) use ($classAndMethod) {
            if (!$response->hasHeader($this->header)) {
                $response = $response->withAddedHeader($this->header, $classAndMethod);
            }

            return $response;
        });
    }
}
