<?php

declare(strict_types=1);

namespace Gamez\Mite\Api;

use Gamez\Mite\Exception\ApiError;
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
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var array
     */
    private $defaultHeaders;

    public function __construct(string $accountName, string $apiKey, ClientInterface $client, RequestFactoryInterface $requestFactory, array $options = null)
    {
        $options = $options ?: [];
        $userAgents = array_filter([$options['User-Agent'] ?? null, self::USER_AGENT]);

        $this->apiHost = "{$accountName}.mite.yo.lk";
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->defaultHeaders = [
            'Accept' => 'application/json',
            'X-MiteApiKey' => $apiKey,
            'User-Agent' => implode(' ', $userAgents),
        ];
    }

    public function get(string $endpoint, array $params = null): ResponseInterface
    {
        return $this->request('GET', $endpoint, $params);
    }

    public function post($endpoint, array $data = null): ResponseInterface
    {
        return $this->request('POST', $endpoint, $data);
    }

    public function patch(string $endpoint, array $data = null): ResponseInterface
    {
        return $this->request('PATCH', $endpoint, $data);
    }

    public function delete(string $endpoint): ResponseInterface
    {
        return $this->request('DELETE', $endpoint);
    }

    private function request(string $method, string $endpoint, array $params = null, array $data = null): ResponseInterface
    {
        $url = $this->createUrl($endpoint, $params);

        $headers = $this->defaultHeaders;

        $body = '';
        if (!empty($data)) {
            $body = JSON::encode($data);
            $headers['Content-Type'] = 'application/json';
        }

        $request = $this->createRequest($method, $url, $headers, $body);

        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw ApiError::fromRequestAndReason($request, "Unable to send request to send {$method} request to {$endpoint}", $e);
        }

        if ($response->getStatusCode() >= 400) {
            throw ApiError::fromRequestAndResponse($request, $response);
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
