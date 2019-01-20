<?php

declare(strict_types=1);

namespace Gamez\Mite\Api;

use Gamez\Mite\Exception\ApiError;
use Gamez\Mite\Support\JSON;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class GuzzleApiClient implements ApiClient
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
     * @var array
     */
    private $defaultHeaders;

    /**
     * @var GuzzleClientInterface
     */
    private $client;

    public function __construct(string $accountName, string $apiKey, array $options = null, GuzzleClientInterface $client = null)
    {
        $options = $options ?: [];
        $userAgents = array_filter([$options['User-Agent'] ?? null, self::USER_AGENT]);

        $this->apiHost = "{$accountName}.mite.yo.lk";
        $this->defaultHeaders = [
            'Accept' => 'application/json',
            'X-MiteApiKey' => $apiKey,
            'User-Agent' => implode(' ', $userAgents),
        ];
        $this->client = $client ?: new GuzzleClient();
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
            $response = $this->client->send($request);
        } catch (ConnectException $e) {
            throw ApiError::fromRequestAndReason($e->getRequest(), "Unable to connect to API: {$e->getMessage()}", $e);
        } catch (RequestException $e) {
            if ($response = $e->getResponse()) {
                throw ApiError::fromRequestAndResponse($e->getRequest(), $response);
            }
            throw ApiError::fromRequestAndReason($e->getRequest(), 'The API returned an error');
        } catch (GuzzleException $e) {
            throw ApiError::fromRequestAndReason($request, 'The API returned an error');
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
        $body = $body ?: '';

        $request = new Request($method, $url);
        $request->getBody()->write($body);

        foreach ($headers as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }

        return $request;
    }
}
