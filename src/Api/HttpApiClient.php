<?php

declare(strict_types=1);

namespace Gamez\Mite\Api;

use Beste\Json;
use Gamez\Mite\Exception\ApiClientError;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class HttpApiClient implements ApiClient
{
    /**
     * @param non-empty-string $apiHost
     * @param non-empty-string $apiKey
     */
    private function __construct(
        private readonly string $apiHost,
        private readonly string $apiKey,
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
    ) {
    }

    /**
     * @param non-empty-string $accountName
     * @param non-empty-string $apiKey
     */
    public static function with(string $accountName, string $apiKey, ClientInterface $client, RequestFactoryInterface $requestFactory): self
    {
        return new self(
            apiHost: "{$accountName}.mite.de",
            apiKey: $apiKey,
            client: $client,
            requestFactory: $requestFactory,
        );
    }

    public function head(string $endpoint, ?array $params = null): ResponseInterface
    {
        return $this->request('HEAD', $endpoint, $params);
    }

    public function get(string $endpoint, ?array $params = null): ResponseInterface
    {
        return $this->request('GET', $endpoint, $params);
    }

    public function post(string $endpoint, ?array $data = null): ResponseInterface
    {
        return $this->request('POST', $endpoint, null, $data);
    }

    public function patch(string $endpoint, ?array $data = null): ResponseInterface
    {
        return $this->request('PATCH', $endpoint, null, $data);
    }

    public function delete(string $endpoint, ?array $params = null): ResponseInterface
    {
        return $this->request('DELETE', $endpoint, $params);
    }

    /**
     * @param non-empty-string $method
     * @param non-empty-string $endpoint
     * @param array<non-empty-string, numeric|string>|null $params
     * @param array<non-empty-string, mixed>|null $data
     */
    private function request(string $method, string $endpoint, ?array $params = null, ?array $data = null): ResponseInterface
    {
        $url = $this->createUrl($endpoint, $params);

        $headers = [
            'Accept' => 'application/json',
            'X-MiteApiKey' => $this->apiKey,
            'User-Agent' => self::USER_AGENT,
        ];

        $body = null;
        if (null !== $data) {
            $body = JSON::encode($data);
            assert('' !== $body);

            $headers['Content-Type'] = 'application/json';
        }

        $request = $this->createRequest($method, $url, $headers, $body);

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw ApiClientError::fromRequestAndReason($request, "Unable to send request to send {$method} request to {$endpoint}", $e);
        }

        if ($response->getStatusCode() >= 400) {
            throw ApiClientError::fromRequestAndResponse($request, $response);
        }

        return $response;
    }

    /**
     * @param non-empty-string $endpoint
     * @param array<non-empty-string, mixed>|null $params
     *
     * @return non-empty-string
     */
    private function createUrl(string $endpoint, array $params = null): string
    {
        $url = "https://{$this->apiHost}/{$endpoint}.json";

        if (!empty($params)) {
            $url .= '?'.http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        }

        return $url;
    }

    /**
     * @param non-empty-string $method
     * @param non-empty-string $url
     * @param array<non-empty-string, non-empty-string|list<non-empty-string>> $headers
     * @param non-empty-string|null $body
     */
    private function createRequest(string $method, string $url, array $headers, ?string $body = null): RequestInterface
    {
        $request = $this->requestFactory->createRequest($method, $url);

        if (null !== $body) {
            $request->getBody()->write($body);
        }

        foreach ($headers as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }

        return $request;
    }
}
