<?php

declare(strict_types=1);

namespace Gamez\Mite\Api;

use Gamez\Mite\Exception\ApiClientError;
use Gamez\Mite\Support\JSON;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class HttpApiClient implements ApiClient
{
    /**
     * @var string
     */
    private $apiScheme = 'https';

    /**
     * @var string
     */
    private $apiHost;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    private function __construct()
    {
    }

    public static function with(string $accountName, string $apiKey, ClientInterface $client, RequestFactoryInterface $requestFactory): self
    {
        $that = new self();

        $that->apiHost = "{$accountName}.mite.yo.lk";
        $that->apiKey = $apiKey;
        $that->client = $client;
        $that->requestFactory = $requestFactory;

        return $that;
    }

    public function head(string $endpoint, array $params = null): ResponseInterface
    {
        return $this->request('HEAD', $endpoint, $params);
    }

    public function get(string $endpoint, array $params = null): ResponseInterface
    {
        return $this->request('GET', $endpoint, $params);
    }

    public function post(string $endpoint, array $data = null): ResponseInterface
    {
        return $this->request('POST', $endpoint, $data);
    }

    public function patch(string $endpoint, array $data = null): ResponseInterface
    {
        return $this->request('PATCH', $endpoint, $data);
    }

    public function delete(string $endpoint, array $params = null): ResponseInterface
    {
        return $this->request('DELETE', $endpoint, $params);
    }

    private function request(string $method, string $endpoint, array $params = null, array $data = null): ResponseInterface
    {
        $url = $this->createUrl($endpoint, $params);

        $headers = [
            'Accept' => 'application/json',
            'X-MiteApiKey' => $this->apiKey,
            'User-Agent' => self::USER_AGENT,
        ];

        $body = '';
        if (!empty($data)) {
            $body = JSON::encode($data);
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

    private function createUrl(string $endpoint, array $params = null): string
    {
        $url = "{$this->apiScheme}://{$this->apiHost}/{$endpoint}";

        if (!empty($params)) {
            $url .= '?'.http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        }

        return $url;
    }

    private function createRequest(string $method, string $url, array $headers, string $body = ''): RequestInterface
    {
        $request = $this->requestFactory->createRequest($method, $url);
        $request->getBody()->write($body);

        foreach ($headers as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }

        return $request;
    }
}
