<?php

declare(strict_types=1);

namespace Gamez\Mite\Exception;

use Beste\Json;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class ApiClientError extends \RuntimeException implements MiteException
{
    private RequestInterface $request;
    private ?ResponseInterface $response;

    public function __construct(
        RequestInterface $request,
        ?ResponseInterface $response,
        ?string $message = null,
        ?int $code = null,
        ?\Throwable $previous = null,
    ) {
        $this->request = $request;
        $this->response = $response;
        $code ??= 0;

        if ($response) {
            $message = $message ?: $response->getReasonPhrase();
            $code = $response->getStatusCode();
        } elseif ($previous) {
            $message = $message ?: $previous->getMessage();
            $code = $previous->getCode();
        } else {
            $message = $message ?: 'An API error occurred';
        }

        parent::__construct($message, $code, $previous);
    }

    public static function fromRequestAndReason(RequestInterface $request, string $reason, ?\Throwable $previous = null): self
    {
        return new self($request, null, $reason, null, $previous);
    }

    public static function fromRequestAndResponse(RequestInterface $request, ResponseInterface $response, ?\Throwable $previous = null): self
    {
        $code = $response->getStatusCode();

        try {
            $data = JSON::decode((string) $response->getBody(), true);
        } catch (\Throwable $e) {
            $data = [];
        }
        $message = $data['error'] ?? null;

        return new self($request, $response, $message, $code, $previous);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function hasResponse(): bool
    {
        return (bool) $this->response;
    }
}
